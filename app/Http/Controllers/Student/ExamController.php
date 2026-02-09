<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * عرض قائمة الامتحانات المتاحة للطالب
     */
    public function index()
    {
        $user = Auth::user();

        // أرقام الكورسات التي الطالب مسجّل فيها (نشط أو في الانتظار) لظهور الامتحانات
        $enrolledCourseIds = $user->courseEnrollments()
            ->whereIn('status', ['active', 'pending'])
            ->pluck('advanced_course_id')
            ->unique()
            ->values()
            ->toArray();

        $availableExams = Exam::whereIn('advanced_course_id', $enrolledCourseIds)
                             ->where('is_active', true)
                             ->where('is_published', true)
                             ->where(function ($q) {
                                 $q->whereNull('start_time')->orWhere('start_time', '<=', now());
                             })
                             ->where(function ($q) {
                                 $q->whereNull('end_time')->orWhere('end_time', '>=', now());
                             })
                             ->with(['course.academicSubject', 'lesson'])
                             ->orderBy('created_at', 'desc')
                             ->get();

        // إضافة معلومات المحاولات لكل امتحان
        $availableExams->each(function($exam) use ($user) {
            $exam->user_attempts = $exam->attempts()->where('user_id', $user->id)->count();
            $exam->can_attempt = $exam->canAttempt($user->id);
            $exam->last_attempt = $exam->getLastAttempt($user->id);
            $exam->best_score = $exam->getBestScore($user->id);
        });

        return view('student.exams.index', compact('availableExams'));
    }

    /**
     * عرض تفاصيل الامتحان قبل البدء
     */
    public function show(Exam $exam)
    {
        $user = Auth::user();

        // التحقق من إمكانية الوصول للامتحان (تسجيل نشط فقط)
        if (!$user->isEnrolledIn($exam->advanced_course_id)) {
            $enrollment = $user->getCourseEnrollment($exam->advanced_course_id);
            $message = $enrollment && $enrollment->status === 'pending'
                ? 'تسجيلك في هذا الكورس لا يزال في انتظار التفعيل. بعد التفعيل يمكنك أداء الامتحان.'
                : 'يجب التسجيل في الكورس أولاً للوصول للامتحان.';
            return redirect()->route('student.exams.index')
                ->with('error', $message);
        }

        if (!$exam->isAvailable()) {
            return redirect()->route('student.exams.index')
                ->with('error', 'الامتحان غير متاح حالياً');
        }

        if (!$exam->canAttempt($user->id)) {
            return redirect()->route('student.exams.index')
                ->with('error', 'لقد استنفدت عدد المحاولات المسموحة');
        }

        $exam->load(['course.academicSubject', 'lesson', 'examQuestions']);
        
        // معلومات المحاولات السابقة
        $previousAttempts = $exam->attempts()
                               ->where('user_id', $user->id)
                               ->orderBy('created_at', 'desc')
                               ->get();

        return view('student.exams.show', compact('exam', 'previousAttempts'));
    }

    /**
     * بدء الامتحان
     */
    public function start(Exam $exam)
    {
        $user = Auth::user();

        // التحقق من الصلاحيات
        if (!$user->isEnrolledIn($exam->advanced_course_id) || !$exam->canAttempt($user->id)) {
            return redirect()->route('student.exams.index')
                ->with('error', 'غير مصرح لك ببدء هذا الامتحان');
        }

        // التحقق من أن الامتحان يحتوي على أسئلة
        $questionsCount = $exam->examQuestions()->count();
        if ($questionsCount === 0) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'هذا الامتحان لا يحتوي على أسئلة بعد. يرجى مراجعة المشرف.');
        }

        // التحقق من أن مدة الامتحان محددة وغير صفرية (تجنب فتح النتيجة فوراً)
        if (empty($exam->duration_minutes) || (int) $exam->duration_minutes <= 0) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'مدة الامتحان غير محددة. يرجى مراجعة المشرف.');
        }

        // التحقق من عدم وجود محاولة جارية
        $activeAttempt = $exam->attempts()
                            ->where('user_id', $user->id)
                            ->where('status', 'in_progress')
                            ->first();

        if ($activeAttempt) {
            return redirect()->route('student.exams.take', [$exam, $activeAttempt]);
        }

        // إنشاء محاولة جديدة
        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'started_at' => now(),
            'status' => 'in_progress',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'answers' => [],
            'tab_switches' => 0,
            'suspicious_activities' => [],
        ]);

        return redirect()->route('student.exams.take', [$exam, $attempt]);
    }

    /**
     * أداء الامتحان
     */
    public function take(Exam $exam, ExamAttempt $attempt)
    {
        $user = Auth::user();

        // التحقق من الصلاحيات
        if ($attempt->user_id !== $user->id || $attempt->exam_id !== $exam->id) {
            return redirect()->route('student.exams.index')
                ->with('error', 'غير مصرح لك بالوصول لهذه المحاولة');
        }

        // التحقق من حالة المحاولة
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.result', [$exam, $attempt]);
        }

        // التحقق من انتهاء الوقت
        if ($attempt->isTimeExpired()) {
            return $this->autoSubmit($exam, $attempt);
        }

        $exam->load(['examQuestions.question.category']);

        // عدم السماح بفتح امتحان بدون أسئلة
        if ($exam->examQuestions->isEmpty()) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'هذا الامتحان لا يحتوي على أسئلة.');
        }

        // ترتيب الأسئلة
        $questions = $exam->examQuestions;
        if ($exam->randomize_questions) {
            $questions = $questions->shuffle();
        }

        return view('student.exams.take', compact('exam', 'attempt', 'questions'));
    }

    /**
     * حفظ إجابة
     */
    public function saveAnswer(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id || $attempt->status !== 'in_progress') {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        if ($attempt->isTimeExpired()) {
            return response()->json(['error' => 'انتهى الوقت المحدد'], 410);
        }

        $answers = $attempt->answers ?? [];
        $answers[$request->question_id] = $request->answer;

        $attempt->update(['answers' => $answers]);

        return response()->json(['success' => true, 'message' => 'تم حفظ الإجابة']);
    }

    /**
     * تسليم الامتحان
     * إذا انتهى وقت الامتحان يُعتبر تسليم تلقائي
     */
    public function submit(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id || $attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.index')
                ->with('error', 'غير مصرح لك بتسليم هذا الامتحان');
        }

        $autoSubmitted = $attempt->isTimeExpired();

        return $this->completeAttempt($exam, $attempt, $autoSubmitted);
    }

    /**
     * تسليم تلقائي عند انتهاء الوقت
     */
    public function autoSubmit(Exam $exam, ExamAttempt $attempt)
    {
        return $this->completeAttempt($exam, $attempt, true);
    }

    /**
     * إكمال المحاولة وحساب النتيجة
     */
    private function completeAttempt(Exam $exam, ExamAttempt $attempt, $autoSubmitted = false)
    {
        $timeTaken = max(0, (int) now()->diffInSeconds($attempt->started_at, true));

        $attempt->update([
            'status' => $autoSubmitted ? 'auto_submitted' : 'submitted',
            'submitted_at' => now(),
            'time_taken' => $timeTaken,
            'auto_submitted' => $autoSubmitted,
        ]);

        // حساب النتيجة
        $attempt->calculateScore();

        if ($exam->show_results_immediately) {
            return redirect()->route('student.exams.result', [$exam, $attempt]);
        }

        return redirect()->route('student.exams.index')
            ->with('success', 'تم تسليم الامتحان بنجاح. ستظهر النتيجة لاحقاً.');
    }

    /**
     * عرض نتيجة الامتحان
     */
    public function result(Exam $exam, ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id) {
            return redirect()->route('student.exams.index')
                ->with('error', 'غير مصرح لك بعرض هذه النتيجة');
        }

        if (!$exam->show_results_immediately && $attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.index')
                ->with('info', 'ستظهر النتيجة لاحقاً');
        }

        $attempt->load(['exam.examQuestions.question']);

        return view('student.exams.result', compact('exam', 'attempt'));
    }

    /**
     * تسجيل تبديل التبويب
     */
    public function logTabSwitch(Exam $exam, ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id || $attempt->status !== 'in_progress') {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $attempt->incrementTabSwitches();

        // إنهاء الامتحان إذا كان منع تبديل التبويبات مفعل
        if ($exam->prevent_tab_switch && $attempt->tab_switches >= 3) {
            $this->completeAttempt($exam, $attempt, true);
            return response()->json([
                'exam_ended' => true,
                'message' => 'تم إنهاء الامتحان بسبب تبديل التبويبات المتكرر'
            ]);
        }

        return response()->json([
            'warning' => true,
            'tab_switches' => $attempt->tab_switches,
            'message' => 'تحذير: تم رصد تبديل التبويب. المحاولة رقم ' . $attempt->tab_switches
        ]);
    }
}

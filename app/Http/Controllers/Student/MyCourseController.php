<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\LessonVideoQuestion;
use App\Models\LessonVideoQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyCourseController extends Controller
{
    /**
     * عرض الكورسات المفعلة للطالب
     */
    public function index()
    {
        $user = Auth::user();
        
        // الكورسات المفعلة للطالب
        $activeCourses = $user->activeCourses()
            ->with(['academicYear', 'academicSubject', 'teacher', 'lessons'])
            ->paginate(12);

        // إحصائيات
        $stats = [
            'total_active' => $user->activeCourses()->count(),
            'total_completed' => $user->courseEnrollments()->where('status', 'completed')->count(),
            'total_hours' => $user->activeCourses()->sum('duration_hours'),
            'avg_progress' => $this->calculateAverageProgress($user),
        ];

        return view('student.my-courses.index', compact('activeCourses', 'stats'));
    }

    /**
     * عرض تفاصيل الكورس المفعل (وضع التركيز: سايدبار أقسام/دروس + محتوى)
     */
    public function show($courseId)
    {
        $user = Auth::user();
        
        $course = $user->activeCourses()
            ->with([
                'academicYear', 'academicSubject', 'teacher',
                'sections' => fn($q) => $q->ordered()->with(['lessons' => fn($q) => $q->ordered()]),
                'lessons' => fn($q) => $q->ordered()->with(['progress' => fn($q) => $q->where('user_id', $user->id)])
            ])
            ->findOrFail($courseId);

        // دروس بدون قسم (للعرض تحت الأقسام)
        $lessonsWithoutSection = $course->lessons->whereNull('course_section_id')->sortBy('order')->values();

        // دالة: هل الدرس يُعتبر مكتملاً حسب إعدادات الكورس (للوضع التسلسلي)
        $requiredPercent = $course->getRequiredWatchPercent();
        $isLessonConsideredCompleted = function ($lesson) use ($requiredPercent) {
            if ($lesson->progress->isEmpty()) return false;
            $p = $lesson->progress->first();
            if ($p->is_completed) return true;
            $durationSeconds = max(1, (int) ($lesson->duration_minutes ?? 0) * 60);
            $watchPercent = $durationSeconds > 0 ? min(100, ($p->watch_time / $durationSeconds) * 100) : 0;
            return $watchPercent >= $requiredPercent;
        };

        // بناء قائمة مرتبة: أقسام ثم دروس بدون قسم، وتحديد المكتمل/الحالي
        $totalLessons = $course->lessons->where('is_active', true)->count();
        $completedLessons = $course->lessons->filter(function ($lesson) use ($isLessonConsideredCompleted) {
            return $isLessonConsideredCompleted($lesson);
        })->count();
        $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0;

        $orderedLessonIds = $course->sections->flatMap(fn ($s) => $s->lessons->pluck('id'))->merge($lessonsWithoutSection->pluck('id'))->filter()->values()->all();
        $activeOrderedIds = array_values(array_filter($orderedLessonIds, function ($lid) use ($course) {
            $lesson = $course->lessons->firstWhere('id', $lid);
            return $lesson && $lesson->is_active;
        }));

        // أول درس يمكن فتحه + الدروس التي يمكن فتحها (حسب وضع الكورس: تسلسلي أو حر)
        $firstUnlockedLessonId = null;
        $canOpenLessonIds = [];
        if ($course->isStrictLessonAccess()) {
            foreach ($activeOrderedIds as $lid) {
                $lesson = $course->lessons->firstWhere('id', $lid);
                if (!$lesson) continue;
                $allPrevDone = true;
                foreach ($activeOrderedIds as $pid) {
                    if ($pid == $lid) break;
                    $prev = $course->lessons->firstWhere('id', $pid);
                    if ($prev && !$isLessonConsideredCompleted($prev)) {
                        $allPrevDone = false;
                        break;
                    }
                }
                if ($allPrevDone) {
                    $canOpenLessonIds[] = $lid;
                    if ($firstUnlockedLessonId === null && !$isLessonConsideredCompleted($lesson)) {
                        $firstUnlockedLessonId = $lid;
                    }
                }
            }
        } else {
            // وضع حر: كل الدروس النشطة مفتوحة
            $canOpenLessonIds = $activeOrderedIds;
            $firstUnlockedLessonId = $activeOrderedIds[0] ?? null;
        }
        $canOpenLessonIds = array_flip($canOpenLessonIds);
        if ($firstUnlockedLessonId === null && !empty($activeOrderedIds)) {
            $firstUnlockedLessonId = $activeOrderedIds[0];
        }
        $selectedLessonId = (int) request()->query('lesson', $firstUnlockedLessonId ?: ($activeOrderedIds[0] ?? 0));

        return view('student.my-courses.show', compact(
            'course', 'progress', 'totalLessons', 'completedLessons',
            'lessonsWithoutSection', 'orderedLessonIds', 'selectedLessonId', 'canOpenLessonIds', 'requiredPercent'
        ));
    }

    /**
     * عرض الدرس في واجهة محمية
     */
    public function watchLesson($courseId, $lessonId)
    {
        $user = Auth::user();
        
        // التحقق من أن الطالب مسجل في الكورس
        $course = $user->activeCourses()->findOrFail($courseId);
        $lesson = $course->lessons()->findOrFail($lessonId);
        
        // التحقق من أن الدرس نشط
        if (!$lesson->is_active) {
            return redirect()->route('my-courses.show', $course)
                ->with('error', 'هذا الدرس غير متاح حالياً');
        }
        
        // في الوضع التسلسلي فقط: التحقق من إكمال الدروس السابقة (أو الوصول لنسبة المشاهدة المطلوبة)
        if ($course->isStrictLessonAccess()) {
            $previousLessons = $course->lessons()
                ->where('order', '<', $lesson->order)
                ->where('is_active', true)
                ->get();
            $requiredPercent = $course->getRequiredWatchPercent();
            foreach ($previousLessons as $prevLesson) {
                $prevProgress = \App\Models\LessonProgress::where('user_id', $user->id)
                    ->where('course_lesson_id', $prevLesson->id)
                    ->first();
                $prevDone = $prevProgress && (
                    $prevProgress->is_completed
                    || ($prevLesson->duration_minutes && $prevLesson->duration_minutes > 0
                        && (($prevProgress->watch_time / max(1, $prevLesson->duration_minutes * 60)) * 100) >= $requiredPercent)
                );
                if (!$prevDone) {
                    return redirect()->route('my-courses.show', $course)
                        ->with('error', 'يجب إكمال الدروس السابقة أولاً (أو مشاهدة النسبة المطلوبة)');
                }
            }
        }

        // التقدم المحفوظ للطالب في هذا الدرس (لاستئناف الفيديو من آخر موضع)
        $lessonProgress = \App\Models\LessonProgress::where('user_id', $user->id)
            ->where('course_lesson_id', $lesson->id)
            ->first();
        $savedWatchTime = $lessonProgress ? (int) $lessonProgress->watch_time : 0;

        // نقاط التوقف (أسئلة الفيديو) للعرض والتحقق لاحقاً من الإجابة عبر API
        $videoCheckpoints = [];
        if ($lesson->type === 'video') {
            $lesson->load(['videoQuestions' => fn ($q) => $q->orderBy('time_seconds')->with('question')]);
            foreach ($lesson->videoQuestions as $vq) {
                $q = $vq->question;
                if (!$q) continue;
                $videoCheckpoints[] = [
                    'id' => $vq->id,
                    'time_seconds' => (int) $vq->time_seconds,
                    'on_wrong' => $vq->on_wrong,
                    'question' => [
                        'id' => $q->id,
                        'question' => $q->question,
                        'type' => $q->type,
                        'options' => $q->type === 'true_false' ? ['صح', 'خطأ'] : (is_array($q->options) ? $q->options : []),
                    ],
                ];
            }
        }

        $allowFlexibleSubmission = $lesson->allowsFlexibleSubmission();

        $lessonExams = Exam::query()
            ->where('advanced_course_id', $course->id)
            ->where('course_lesson_id', $lesson->id)
            ->available()
            ->orderBy('created_at', 'desc')
            ->get();
        $lessonExams->each(function (Exam $exam) use ($user) {
            $exam->user_can_attempt = $exam->canAttempt($user->id);
        });

        return view('student.my-courses.lesson-viewer', compact('course', 'lesson', 'videoCheckpoints', 'savedWatchTime', 'allowFlexibleSubmission', 'lessonExams'));
    }

    /**
     * التحقق من إجابة سؤال الفيديو (نقطة توقف)
     */
    public function checkVideoQuestionAnswer(Request $request, $courseId, $lessonId)
    {
        $user = Auth::user();
        $course = $user->activeCourses()->findOrFail($courseId);
        $lesson = $course->lessons()->findOrFail($lessonId);
        $request->validate([
            'video_question_id' => 'required|integer',
            'answer' => 'required',
        ]);
        $videoQuestion = LessonVideoQuestion::where('course_lesson_id', $lesson->id)
            ->where('id', $request->video_question_id)
            ->with('question')
            ->firstOrFail();
        $question = $videoQuestion->question;
        $answer = $request->answer;
        if (is_array($answer)) {
            $answer = json_encode($answer);
        }
        $correct = $question->isCorrectAnswer($answer);
        LessonVideoQuestionAnswer::create([
            'user_id' => $user->id,
            'lesson_video_question_id' => $videoQuestion->id,
            'answer' => is_string($answer) ? $answer : json_encode($answer),
            'is_correct' => (bool) $correct,
        ]);
        return response()->json(['correct' => (bool) $correct]);
    }

    /**
     * تسجيل مدة الفيديو تلقائياً (يُستدعى من مشغّل الدرس عند معرفة المدة)
     */
    public function reportLessonDuration(Request $request, $courseId, $lessonId)
    {
        $user = Auth::user();
        $course = $user->activeCourses()->findOrFail($courseId);
        $lesson = $course->lessons()->findOrFail($lessonId);
        $request->validate([
            'duration_seconds' => 'required|integer|min:1',
        ]);
        $durationMinutes = (int) ceil($request->duration_seconds / 60);
        $lesson->update(['duration_minutes' => $durationMinutes]);
        return response()->json(['success' => true, 'duration_minutes' => $durationMinutes]);
    }

    /**
     * الإبلاغ عن مخالفة (سكرين شوت أو تسجيل شاشة) — يؤدي لتعليق الحساب
     */
    public function reportViolation(Request $request)
    {
        $request->validate([
            'type' => 'required|in:screenshot,recording,other',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // إذا كان الحساب موقوفاً مسبقاً لا نكرر التعليق
        if ($user->isSuspended()) {
            return response()->json(['success' => true, 'already_suspended' => true]);
        }

        \App\Models\AccountViolation::create([
            'user_id' => $user->id,
            'violation_type' => $request->type,
            'notes' => $request->notes,
            'ip_address' => $request->ip(),
        ]);

        $user->update([
            'suspended_at' => now(),
            'suspension_reason' => $request->type,
        ]);

        return response()->json([
            'success' => true,
            'suspended' => true,
            'message' => 'تم تعليق حسابك بسبب مخالفة قواعد الاستخدام. تواصل مع الإدارة لإعادة التفعيل.',
            'redirect' => route('account.suspended'),
        ]);
    }

    /**
     * حساب متوسط التقدم
     */
    private function calculateAverageProgress($user)
    {
        $enrollments = $user->courseEnrollments()->where('status', 'active')->get();
        if ($enrollments->isEmpty()) return 0;
        
        $totalProgress = $enrollments->sum('progress');
        return round($totalProgress / $enrollments->count(), 1);
    }

    /**
     * تحديث تقدم الدرس
     */
    public function updateLessonProgress(Request $request, $courseId, $lessonId)
    {
        $user = Auth::user();
        
        // التحقق من أن الطالب مسجل في الكورس
        $course = $user->activeCourses()->findOrFail($courseId);
        $lesson = $course->lessons()->findOrFail($lessonId);

        $watchTime = $request->input('watch_time', 0);
        $progressPercent = $request->input('progress_percent', 0);
        $requiredPercent = $course->getRequiredWatchPercent();
        // إذا الدرس يسمح بتقديم حر: إكمال عند طلب الطالب (completed=true). وإلا: إكمال عند الوصول لنسبة المشاهدة المطلوبة.
        $isCompleted = $request->boolean('completed')
            || (!$lesson->allowsFlexibleSubmission() && $progressPercent >= $requiredPercent);

        // تحديث أو إنشاء تقدم الدرس
        $progress = \App\Models\LessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_lesson_id' => $lessonId
            ],
            [
                'is_completed' => $isCompleted,
                'completed_at' => $isCompleted ? now() : null,
                'watch_time' => $watchTime
            ]
        );

        // تحديث التقدم الإجمالي للكورس
        $this->updateCourseProgress($user->id, $courseId);

        // إذا الطلب من نموذج عادي (ليس AJAX) نوجّه للدرس مع رسالة نجاح
        if (!$request->wantsJson() && !$request->ajax()) {
            return redirect()->route('my-courses.lesson.watch', [$courseId, $lessonId])
                ->with('success', 'تم تسليم الحصة بنجاح');
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التقدم بنجاح',
            'progress' => $progress,
            'course_progress' => $this->getCourseProgress($user->id, $courseId)
        ]);
    }

    /**
     * الحصول على تقدم الكورس
     */
    private function getCourseProgress($userId, $courseId)
    {
        $course = \App\Models\AdvancedCourse::findOrFail($courseId);
        $totalLessons = $course->lessons()->where('is_active', true)->count();
        
        if ($totalLessons === 0) return 0;

        $completedLessons = \App\Models\LessonProgress::where('user_id', $userId)
            ->whereIn('course_lesson_id', $course->lessons()->where('is_active', true)->pluck('id'))
            ->where('is_completed', true)
            ->count();

        return round(($completedLessons / $totalLessons) * 100, 2);
    }

    /**
     * تحديث التقدم الإجمالي للكورس
     */
    private function updateCourseProgress($userId, $courseId)
    {
        $course = \App\Models\AdvancedCourse::findOrFail($courseId);
        $activeLessonIds = $course->lessons()->where('is_active', true)->pluck('id');
        $totalLessons = $activeLessonIds->count();

        if ($totalLessons > 0) {
            $completedLessons = \App\Models\LessonProgress::where('user_id', $userId)
                ->whereIn('course_lesson_id', $activeLessonIds)
                ->where('is_completed', true)
                ->count();

            $progressPercentage = round(($completedLessons / $totalLessons) * 100, 2);

            \App\Models\StudentCourseEnrollment::where('user_id', $userId)
                ->where('advanced_course_id', $courseId)
                ->update(['progress' => $progressPercentage]);
        }
    }
}

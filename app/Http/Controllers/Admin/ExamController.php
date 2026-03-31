<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use App\Models\AdvancedCourse;
use App\Models\CourseLesson;
use App\Models\ExamQuestion;
use App\Models\ExamExtraAttemptGrant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamController extends Controller
{
    /**
     * عرض قائمة الامتحانات
     */
    public function index(Request $request)
    {
        $query = Exam::with(['course.academicSubject', 'lesson'])
                    ->withCount(['questions', 'attempts']);

        // فلترة حسب الكورس
        if ($request->filled('course_id')) {
            $query->where('advanced_course_id', $request->course_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'published') {
                $query->where('is_published', true);
            }
        }

        // البحث
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(15);

        // بيانات للفلاتر
        $courses = AdvancedCourse::active()->with(['academicSubject'])->get();

        return view('admin.exams.index', compact('exams', 'courses'));
    }

    /**
     * عرض صفحة إنشاء امتحان جديد
     */
    public function create(Request $request)
    {
        $courses = AdvancedCourse::active()->with(['academicSubject', 'academicYear'])->get();
        $selectedCourse = $request->get('course_id');
        $lessons = $selectedCourse ? CourseLesson::where('advanced_course_id', $selectedCourse)->active()->get() : collect();

        return view('admin.exams.create', compact('courses', 'selectedCourse', 'lessons'));
    }

    /**
     * حفظ امتحان جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'advanced_course_id' => 'required|exists:advanced_courses,id',
            'course_lesson_id' => 'nullable|exists:course_lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'attempts_allowed' => 'required|integer|min:0|max:10',
            'passing_marks' => 'required|numeric|min:0|max:100',
            'total_marks' => 'nullable|numeric|min:0',
            'start_time' => 'nullable|date|after:now',
            'end_time' => 'nullable|date|after:start_time',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'show_results_immediately' => 'boolean',
            'show_correct_answers' => 'boolean',
            'show_explanations' => 'boolean',
            'allow_review' => 'boolean',
            'require_camera' => 'boolean',
            'require_microphone' => 'boolean',
            'prevent_tab_switch' => 'boolean',
            'auto_submit' => 'boolean',
            'is_active' => 'boolean',
        ], [
            'advanced_course_id.required' => 'الكورس مطلوب',
            'title.required' => 'عنوان الامتحان مطلوب',
            'duration_minutes.required' => 'مدة الامتحان مطلوبة',
            'duration_minutes.min' => 'مدة الامتحان يجب أن تكون 5 دقائق على الأقل',
            'duration_minutes.max' => 'مدة الامتحان لا يجب أن تتجاوز 8 ساعات',
            'attempts_allowed.required' => 'عدد المحاولات المسموحة مطلوب',
            'passing_marks.required' => 'درجة النجاح مطلوبة',
        ]);

        $data = $request->all();
        
        // تحويل checkboxes
        $booleanFields = [
            'randomize_questions', 'randomize_options', 'show_results_immediately',
            'show_correct_answers', 'show_explanations', 'allow_review',
            'require_camera', 'require_microphone', 'prevent_tab_switch',
            'auto_submit', 'is_active'
        ];

        foreach ($booleanFields as $field) {
            $data[$field] = $request->has($field);
        }

        $data['is_published'] = $request->has('is_published');

        // إضافة المستخدم الحالي كمنشئ للامتحان
        $data['created_by'] = auth()->id();

        // تعيين إجمالي الدرجات إلى 0 إذا لم يتم تحديده (سيتم حسابه لاحقاً عند إضافة الأسئلة)
        $data['total_marks'] = $data['total_marks'] ?? 0;

        $exam = Exam::create($data);
        $course = \App\Models\AdvancedCourse::find($exam->advanced_course_id);

        // إشعار تلقائي لطلاب الكورس: تم إضافة امتحان جديد
        if ($course) {
            \App\Models\Notification::sendToCourseStudents($course->id, [
                'sender_id' => auth()->id(),
                'title' => 'امتحان جديد في الكورس',
                'message' => 'تم إضافة امتحان: «' . $exam->title . '» في كورس «' . $course->title . '». سيتم إعلامك عند تفعيله.',
                'type' => 'exam',
                'priority' => 'normal',
                'target_type' => 'course_students',
                'target_id' => $course->id,
                'action_url' => route('my-courses.show', $course),
                'action_text' => 'فتح الكورس',
            ]);
        }

        return redirect()->route('admin.exams.questions.manage', $exam)
            ->with('success', 'تم إنشاء الامتحان بنجاح. يمكنك الآن إضافة الأسئلة.');
    }

    /**
     * عرض تفاصيل الامتحان
     */
    public function show(Exam $exam)
    {
        $exam->load([
            'course.academicSubject',
            'lesson',
            'examQuestions.question.category',
            'attempts.user',
            'extraAttemptGrants'
        ]);

        return view('admin.exams.show', compact('exam'));
    }

    /**
     * عرض صفحة تعديل الامتحان
     */
    public function edit(Exam $exam)
    {
        $courses = AdvancedCourse::active()->with(['academicSubject', 'academicYear'])->get();
        $lessons = CourseLesson::where('advanced_course_id', $exam->advanced_course_id)->active()->get();

        return view('admin.exams.edit', compact('exam', 'courses', 'lessons'));
    }

    /**
     * تحديث الامتحان
     */
    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'advanced_course_id' => 'required|exists:advanced_courses,id',
            'course_lesson_id' => 'nullable|exists:course_lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'attempts_allowed' => 'required|integer|min:0|max:10',
            'passing_marks' => 'required|numeric|min:0|max:100',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        $data = $request->all();
        
        $booleanFields = [
            'randomize_questions', 'randomize_options', 'show_results_immediately',
            'show_correct_answers', 'show_explanations', 'allow_review',
            'require_camera', 'require_microphone', 'prevent_tab_switch',
            'auto_submit', 'is_active'
        ];

        foreach ($booleanFields as $field) {
            $data[$field] = $request->has($field);
        }
        $data['is_published'] = $request->has('is_published');
        $wasPublished = $exam->is_published;

        $exam->update($data);

        // إشعار تلقائي عند تفعيل (نشر) الامتحان لأول مرة
        if (!$wasPublished && $exam->fresh()->is_published) {
            $course = $exam->course;
            if ($course) {
                \App\Models\Notification::sendToCourseStudents($course->id, [
                    'sender_id' => auth()->id(),
                    'title' => 'تم تفعيل امتحان',
                    'message' => 'تم تفعيل امتحان: «' . $exam->title . '» في كورس «' . $course->title . '». يمكنك أداؤه من كورساتي.',
                    'type' => 'exam',
                    'priority' => 'high',
                    'target_type' => 'course_students',
                    'target_id' => $course->id,
                    'action_url' => route('my-courses.show', $course),
                    'action_text' => 'فتح الكورس',
                ]);
            }
        }

        return redirect()->route('admin.exams.show', $exam)
            ->with('success', 'تم تحديث الامتحان بنجاح');
    }

    /**
     * حذف الامتحان
     */
    public function destroy(Exam $exam)
    {
        // التحقق من عدم وجود محاولات
        if ($exam->attempts()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الامتحان لأنه يحتوي على محاولات طلاب');
        }

        $exam->delete();

        return redirect()->route('admin.exams.index')
            ->with('success', 'تم حذف الامتحان بنجاح');
    }

    /**
     * إدارة أسئلة الامتحان
     */
    public function manageQuestions(Request $request, Exam $exam)
    {
        $exam->load(['examQuestions.question.category']);

        $categoriesQuery = QuestionCategory::active()
            ->with(['academicYear', 'academicSubject', 'questions' => fn($q) => $q->active()]);

        if ($request->filled('academic_year_id')) {
            $categoriesQuery->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->filled('academic_subject_id')) {
            $categoriesQuery->where('academic_subject_id', $request->academic_subject_id);
        }
        if ($request->filled('category_id')) {
            $categoriesQuery->where('id', $request->category_id);
        }

        $categories = $categoriesQuery->orderBy('name')->get();

        $academicYears = AcademicYear::orderBy('order')->orderBy('name')->get();
        $academicSubjects = AcademicSubject::with('academicYear')->orderBy('order')->orderBy('name')->get();

        return view('admin.exams.questions', compact('exam', 'categories', 'academicYears', 'academicSubjects'));
    }

    /**
     * إضافة سؤال للامتحان
     */
    public function addQuestion(Request $request, Exam $exam)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'marks' => 'required|numeric|min:0.5|max:100',
            'time_limit' => 'nullable|integer|min:10|max:600',
            'is_required' => 'boolean',
        ]);

        // التحقق من عدم وجود السؤال مسبقاً
        if ($exam->examQuestions()->where('question_id', $request->question_id)->exists()) {
            return back()->with('error', 'السؤال موجود بالفعل في الامتحان');
        }

        $order = $exam->examQuestions()->max('order') + 1;

        ExamQuestion::create([
            'exam_id' => $exam->id,
            'question_id' => $request->question_id,
            'order' => $order,
            'marks' => $request->marks,
            'time_limit' => $request->time_limit,
            'is_required' => $request->has('is_required'),
        ]);

        // تحديث إجمالي الدرجات
        $exam->update([
            'total_marks' => $exam->calculateTotalMarks()
        ]);

        return back()->with('success', 'تم إضافة السؤال للامتحان بنجاح');
    }

    /**
     * إضافة مجموعة أسئلة للامتحان دفعة واحدة
     */
    public function addQuestionsBulk(Request $request, Exam $exam)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'required|exists:questions,id',
            'default_marks' => 'required|numeric|min:0.5|max:100',
        ]);

        $questionIds = array_unique(array_filter((array) $request->question_ids));
        $defaultMarks = (float) $request->default_marks;
        $existingIds = $exam->examQuestions()->pluck('question_id')->toArray();
        $toAdd = array_diff($questionIds, $existingIds);

        if (empty($toAdd)) {
            return back()->with('error', 'جميع الأسئلة المحددة مضافة بالفعل للامتحان، أو لم يتم تحديد أي سؤال.');
        }

        $order = $exam->examQuestions()->max('order') ?? 0;
        foreach ($toAdd as $questionId) {
            $order++;
            ExamQuestion::create([
                'exam_id' => $exam->id,
                'question_id' => $questionId,
                'order' => $order,
                'marks' => $defaultMarks,
                'time_limit' => null,
                'is_required' => false,
            ]);
        }

        $exam->update(['total_marks' => $exam->calculateTotalMarks()]);

        $addedCount = count($toAdd);
        $skippedCount = count($questionIds) - $addedCount;
        $message = 'تم إضافة ' . $addedCount . ' سؤالاً للامتحان بنجاح.';
        if ($skippedCount > 0) {
            $message .= ' (تم تخطي ' . $skippedCount . ' سؤالاً مضافة مسبقاً)';
        }

        return back()->with('success', $message);
    }

    /**
     * إزالة سؤال من الامتحان
     */
    public function removeQuestion(Exam $exam, ExamQuestion $examQuestion)
    {
        $examQuestion->delete();

        // إعادة ترقيم الأسئلة
        $exam->examQuestions()->orderBy('order')->get()->each(function($question, $index) {
            $question->update(['order' => $index + 1]);
        });

        // تحديث إجمالي الدرجات
        $exam->update([
            'total_marks' => $exam->calculateTotalMarks()
        ]);

        return back()->with('success', 'تم إزالة السؤال من الامتحان بنجاح');
    }

    /**
     * إعادة ترتيب أسئلة الامتحان
     */
    public function reorderQuestions(Request $request, Exam $exam)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|exists:exam_questions,id',
            'questions.*.order' => 'required|integer|min:1',
        ]);

        foreach ($request->questions as $questionData) {
            ExamQuestion::where('id', $questionData['id'])
                      ->where('exam_id', $exam->id)
                      ->update(['order' => $questionData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة ترتيب الأسئلة بنجاح'
        ]);
    }

    /**
     * نشر/إلغاء نشر الامتحان
     */
    public function togglePublish(Exam $exam)
    {
        $exam->update([
            'is_published' => !$exam->is_published
        ]);

        $status = $exam->is_published ? 'تم نشر' : 'تم إلغاء نشر';

        return response()->json([
            'success' => true,
            'message' => $status . ' الامتحان بنجاح',
            'is_published' => $exam->is_published
        ]);
    }

    /**
     * تفعيل/إلغاء تفعيل الامتحان
     */
    public function toggleStatus(Exam $exam)
    {
        $exam->update([
            'is_active' => !$exam->is_active
        ]);

        $status = $exam->is_active ? 'تم تفعيل' : 'تم إلغاء تفعيل';

        return response()->json([
            'success' => true,
            'message' => $status . ' الامتحان بنجاح',
            'is_active' => $exam->is_active
        ]);
    }

    /**
     * السماح بمحاولة إضافية لطالب (المحاولة الأولى تظل محفوظة)
     */
    public function grantAttempt(Exam $exam, User $user)
    {
        $exam->load('attempts');

        // التحقق من أن الطالب لديه محاولة سابقة على هذا الامتحان
        $hasAttempt = $exam->attempts()->where('user_id', $user->id)->exists();
        if (!$hasAttempt) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'الطالب لم يقم بأي محاولة على هذا الامتحان بعد.'], 422);
            }
            return back()->with('error', 'الطالب لم يقم بأي محاولة على هذا الامتحان بعد.');
        }

        ExamExtraAttemptGrant::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'granted_by' => auth()->id(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم السماح للطالب بمحاولة إضافية. المحاولة السابقة تظل محفوظة.',
            ]);
        }

        return back()->with('success', 'تم السماح للطالب بمحاولة إضافية. المحاولة السابقة تظل محفوظة.');
    }

    /**
     * إحصائيات الامتحان
     */
    public function statistics(Exam $exam)
    {
        $exam->load(['attempts.user']);

        $stats = [
            'overview' => $exam->stats,
            'attempts_by_date' => $exam->attempts()
                                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                    ->groupBy('date')
                                    ->orderBy('date')
                                    ->get(),
            'score_distribution' => $exam->attempts()
                                       ->completed()
                                       ->selectRaw('
                                           CASE 
                                               WHEN percentage >= 90 THEN "ممتاز"
                                               WHEN percentage >= 80 THEN "جيد جداً"
                                               WHEN percentage >= 70 THEN "جيد"
                                               WHEN percentage >= 60 THEN "مقبول"
                                               ELSE "ضعيف"
                                           END as grade,
                                           COUNT(*) as count
                                       ')
                                       ->groupBy('grade')
                                       ->get(),
        ];

        return view('admin.exams.statistics', compact('exam', 'stats'));
    }

    /**
     * معاينة الامتحان
     */
    public function preview(Exam $exam)
    {
        $exam->load(['examQuestions.question']);
        
        return view('admin.exams.preview', compact('exam'));
    }

    /**
     * نسخ الامتحان
     */
    public function duplicate(Exam $exam)
    {
        DB::beginTransaction();
        
        try {
            $newExam = $exam->replicate();
            $newExam->title = $exam->title . ' - نسخة';
            $newExam->is_active = false;
            $newExam->is_published = false;
            $newExam->save();

            // نسخ الأسئلة
            foreach ($exam->examQuestions as $examQuestion) {
                ExamQuestion::create([
                    'exam_id' => $newExam->id,
                    'question_id' => $examQuestion->question_id,
                    'order' => $examQuestion->order,
                    'marks' => $examQuestion->marks,
                    'time_limit' => $examQuestion->time_limit,
                    'is_required' => $examQuestion->is_required,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.exams.edit', $newExam)
                ->with('success', 'تم نسخ الامتحان بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء نسخ الامتحان');
        }
    }

    /**
     * تصدير جميع محاولات الامتحان إلى ملف Excel بتصميم احترافي
     */
    public function exportAttempts(Exam $exam): StreamedResponse
    {
        $exam->load(['course.academicSubject', 'attempts.user']);
        $attempts = $exam->attempts()->with('user')->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('محاولات الامتحان');
        $sheet->setRightToLeft(true);

        $thinBorder = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]]];

        // صف العنوان الرئيسي
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'تقرير محاولات الامتحان');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A5F');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // اسم الامتحان والكورس
        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', $exam->title . ' — ' . ($exam->course ? $exam->course->title : ''));
        $sheet->getStyle('A2')->getFont()->setSize(12)->getColor()->setRGB('E5E7EB');
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2D4A6F');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(24);

        // تاريخ التصدير
        $sheet->mergeCells('A3:L3');
        $sheet->setCellValue('A3', 'تاريخ التصدير: ' . now()->translatedFormat('l d F Y - H:i'));
        $sheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setRGB('9CA3AF');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(20);

        $completed = $attempts->whereIn('status', ['submitted', 'auto_submitted']);
        $passed = $completed->filter(fn ($a) => (float)($a->percentage ?? 0) >= (float)$exam->passing_marks);
        $avgScore = $completed->count() > 0 ? round($completed->avg('percentage'), 1) : 0;
        $passRate = $completed->count() > 0 ? round(($passed->count() / $completed->count()) * 100, 1) : 0;

        // صف الملخص
        $sheet->setCellValue('A5', 'إجمالي المحاولات');
        $sheet->setCellValue('B5', $attempts->count());
        $sheet->setCellValue('C5', 'مكتملة');
        $sheet->setCellValue('D5', $completed->count());
        $sheet->setCellValue('E5', 'ناجحة');
        $sheet->setCellValue('F5', $passed->count());
        $sheet->setCellValue('G5', 'متوسط النسبة %');
        $sheet->setCellValue('H5', $avgScore);
        $sheet->setCellValue('I5', 'معدل النجاح %');
        $sheet->setCellValue('J5', $passRate);
        $sheet->getStyle('A5:J5')->getFont()->setBold(true);
        $sheet->getStyle('A5:J5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F6');
        $sheet->getStyle('A5:J5')->applyFromArray($thinBorder);

        // رأس جدول المحاولات (صف 7)
        $headers = ['ر.ت', 'الطالب', 'البريد الإلكتروني', 'الهاتف', 'النتيجة', 'النسبة %', 'الحالة', 'الوقت المستغرق', 'تاريخ البدء', 'تاريخ التسليم', 'تسليم تلقائي', 'تبديل التبويب'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '7', $h);
            $col++;
        }
        $sheet->getStyle('A7:L7')->getFont()->setBold(true)->setSize(11)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A7:L7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F46E5');
        $sheet->getStyle('A7:L7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getStyle('A7:L7')->applyFromArray($thinBorder);
        $sheet->getRowDimension(7)->setRowHeight(26);

        $row = 8;
        $n = 1;
        foreach ($attempts as $attempt) {
            $sheet->setCellValue('A' . $row, $n);
            $sheet->setCellValue('B' . $row, $attempt->user ? $attempt->user->name : '—');
            $sheet->setCellValue('C' . $row, $attempt->user ? $attempt->user->email : '—');
            $sheet->setCellValue('D' . $row, $attempt->user && $attempt->user->phone ? $attempt->user->phone : '—');
            $sheet->setCellValue('E' . $row, in_array($attempt->status, ['submitted', 'auto_submitted']) ? (float) $attempt->score . ' / ' . $exam->total_marks : '—');
            $sheet->setCellValue('F' . $row, in_array($attempt->status, ['submitted', 'auto_submitted']) ? round($attempt->percentage, 1) : '—');
            $sheet->setCellValue('G' . $row, $attempt->result_status);
            $sheet->setCellValue('H' . $row, $attempt->formatted_time);
            $sheet->setCellValue('I' . $row, $attempt->started_at ? $attempt->started_at->format('Y-m-d H:i') : '—');
            $sheet->setCellValue('J' . $row, $attempt->submitted_at ? $attempt->submitted_at->format('Y-m-d H:i') : '—');
            $sheet->setCellValue('K' . $row, $attempt->auto_submitted ? 'نعم' : 'لا');
            $sheet->setCellValue('L' . $row, $attempt->tab_switches ?? 0);

            $fillColor = $row % 2 === 0 ? 'F9FAFB' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':L' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fillColor);
            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray($thinBorder);
            $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB(
                $attempt->result_color === 'green' ? '047857' : ($attempt->result_color === 'red' ? 'B91C1C' : '6B7280')
            );
            $row++;
            $n++;
        }

        foreach (range('A', 'L') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('H')->setWidth(14);

        $sheet->freezePane('A8');

        $safeTitle = preg_replace('/[^\p{L}\p{N}\-_]/u', '_', $exam->title);
        $fileName = 'محاولات_' . $safeTitle . '_' . now()->format('Y-m-d-His') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
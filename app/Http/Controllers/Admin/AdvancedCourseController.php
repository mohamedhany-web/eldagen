<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use App\Models\User;
use Illuminate\Http\Request;

class AdvancedCourseController extends Controller
{
    /**
     * عرض قائمة الكورسات
     */
    public function index(Request $request)
    {
        $query = AdvancedCourse::with(['academicYear', 'academicSubject'])
            ->withCount(['lessons', 'enrollments', 'orders']);

        // فلترة حسب السنة الدراسية
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // فلترة حسب المادة
        if ($request->filled('academic_subject_id')) {
            $query->where('academic_subject_id', $request->academic_subject_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // البحث في العنوان
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(15);

        // بيانات للفلاتر
        $academicYears = AcademicYear::where('is_active', true)->get();
        $academicSubjects = AcademicSubject::where('is_active', true)->get();

        return view('admin.advanced-courses.index', compact('courses', 'academicYears', 'academicSubjects'));
    }

    /**
     * عرض صفحة إنشاء كورس جديد
     */
    public function create()
    {
        $academicYears = AcademicYear::where('is_active', true)->get();
        $academicSubjects = AcademicSubject::where('is_active', true)->get();

        return view('admin.advanced-courses.create', compact('academicYears', 'academicSubjects'));
    }

    /**
     * حفظ كورس جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'academic_year_id' => 'required|exists:academic_years,id',
            'academic_subject_id' => 'required|exists:academic_subjects,id',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'duration_hours' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'lesson_access_mode' => 'nullable|in:strict,flexible',
            'required_watch_percent' => 'nullable|integer|min:1|max:100',
        ], [
            'title.required' => 'عنوان الكورس مطلوب',
            'title.max' => 'عنوان الكورس لا يجب أن يتجاوز 255 حرف',
            'academic_year_id.required' => 'السنة الدراسية مطلوبة',
            'academic_year_id.exists' => 'السنة الدراسية المحددة غير موجودة',
            'academic_subject_id.required' => 'المادة الدراسية مطلوبة',
            'academic_subject_id.exists' => 'المادة الدراسية المحددة غير موجودة',
            'level.in' => 'مستوى الكورس غير صحيح',
            'duration_hours.numeric' => 'مدة الكورس يجب أن تكون رقم',
            'duration_hours.min' => 'مدة الكورس لا يمكن أن تكون أقل من صفر',
            'price.numeric' => 'السعر يجب أن يكون رقم',
            'price.min' => 'السعر لا يمكن أن يكون أقل من صفر',
            'thumbnail.image' => 'يجب أن تكون صورة صحيحة',
            'thumbnail.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png أو jpg',
            'thumbnail.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
            'starts_at.date' => 'تاريخ البداية غير صحيح',
            'ends_at.date' => 'تاريخ النهاية غير صحيح',
            'ends_at.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
        ]);

        $data = $request->all();
        
        // معالجة الصورة
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        // تحويل القيم المنطقية
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');
        
        // تعيين القيم الافتراضية
        $data['level'] = $data['level'] ?? 'beginner';
        $data['price'] = $data['price'] ?? 0;
        $data['duration_hours'] = $data['duration_hours'] ?? 0;
        $data['lesson_access_mode'] = $data['lesson_access_mode'] ?? 'strict';
        $data['required_watch_percent'] = $request->lesson_access_mode === 'flexible' ? null : ($data['required_watch_percent'] ?? 90);

        AdvancedCourse::create($data);

        return redirect()->route('admin.advanced-courses.index')
            ->with('success', 'تم إنشاء الكورس بنجاح');
    }

    /**
     * عرض تفاصيل كورس محدد
     */
    public function show(AdvancedCourse $advancedCourse)
    {
        $advancedCourse->load([
            'academicYear', 
            'academicSubject', 
            'teacher',
            'lessons' => function($query) {
                $query->ordered();
            },
            'enrollments.student',
            'orders' => function($query) {
                $query->with(['user'])->orderBy('created_at', 'desc');
            }
        ]);

        // إحصائيات
        $stats = [
            'total_lessons' => $advancedCourse->lessons->count(),
            'active_lessons' => $advancedCourse->lessons->where('is_active', true)->count(),
            'total_students' => $advancedCourse->enrollments->count(),
            'active_students' => $advancedCourse->enrollments->where('status', 'active')->count(),
            'pending_orders' => $advancedCourse->orders->where('status', 'pending')->count(),
            'total_duration' => $advancedCourse->lessons->sum('duration_minutes'),
        ];

        return view('admin.advanced-courses.show', compact('advancedCourse', 'stats'));
    }

    /**
     * عرض صفحة تعديل كورس
     */
    public function edit(AdvancedCourse $advancedCourse)
    {
        $academicYears = AcademicYear::where('is_active', true)->get();
        $academicSubjects = AcademicSubject::where('is_active', true)->get();

        return view('admin.advanced-courses.edit', compact('advancedCourse', 'academicYears', 'academicSubjects'));
    }

    /**
     * تحديث بيانات كورس
     */
    public function update(Request $request, AdvancedCourse $advancedCourse)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'academic_year_id' => 'required|exists:academic_years,id',
            'academic_subject_id' => 'required|exists:academic_subjects,id',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'lesson_access_mode' => 'nullable|in:strict,flexible',
            'required_watch_percent' => 'nullable|integer|min:1|max:100',
        ], [
            'title.required' => 'عنوان الكورس مطلوب',
            'title.max' => 'عنوان الكورس لا يجب أن يتجاوز 255 حرف',
            'academic_year_id.required' => 'السنة الدراسية مطلوبة',
            'academic_year_id.exists' => 'السنة الدراسية المحددة غير موجودة',
            'academic_subject_id.required' => 'المادة الدراسية مطلوبة',
            'academic_subject_id.exists' => 'المادة الدراسية المحددة غير موجودة',
            'price.numeric' => 'السعر يجب أن يكون رقم',
            'price.min' => 'السعر لا يمكن أن يكون أقل من صفر',
        ]);

        $data = $request->all();
        if ($request->lesson_access_mode === 'flexible') {
            $data['required_watch_percent'] = null;
        }
        $advancedCourse->update($data);

        return redirect()->route('admin.advanced-courses.index')
            ->with('success', 'تم تحديث الكورس بنجاح');
    }

    /**
     * حذف كورس
     */
    public function destroy(AdvancedCourse $advancedCourse)
    {
        try {
            $advancedCourse->delete();
            return redirect()->route('admin.advanced-courses.index')
                ->with('success', 'تم حذف الكورس بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('admin.advanced-courses.index')
                ->with('error', 'حدث خطأ أثناء حذف الكورس');
        }
    }

    /**
     * تفعيل طالب في الكورس
     */
    public function activateStudent(Request $request, AdvancedCourse $advancedCourse)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // التحقق من عدم وجود الطالب مسبقاً
        if ($advancedCourse->enrollments()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'الطالب مسجل بالفعل في هذا الكورس');
        }

        $advancedCourse->enrollments()->create([
            'user_id' => $request->user_id,
            'enrolled_at' => now(),
            'is_active' => true,
        ]);

        return back()->with('success', 'تم تفعيل الطالب في الكورس بنجاح');
    }

    /**
     * عرض طلاب الكورس
     */
    public function students(AdvancedCourse $advancedCourse)
    {
        $advancedCourse->load(['enrollments.user']);
        $availableStudents = User::where('role', 'student')
            ->whereNotIn('id', $advancedCourse->enrollments->pluck('user_id'))
            ->get();

        return view('admin.advanced-courses.students', compact('advancedCourse', 'availableStudents'));
    }

    /**
     * الحصول على المواد حسب السنة الدراسية
     */
    public function getSubjectsByYear(Request $request)
    {
        $subjects = AcademicSubject::where('academic_year_id', $request->academic_year_id)
            ->where('is_active', true)
            ->select('id', 'name')
            ->get();

        return response()->json($subjects);
    }

    /**
     * تغيير حالة الكورس (تفعيل/إلغاء تفعيل)
     */
    public function toggleStatus(AdvancedCourse $advancedCourse)
    {
        $advancedCourse->update([
            'is_active' => !$advancedCourse->is_active
        ]);

        $status = $advancedCourse->is_active ? 'تم تفعيل' : 'تم إيقاف';

        return response()->json([
            'success' => true,
            'message' => $status . ' الكورس بنجاح',
            'is_active' => $advancedCourse->is_active
        ]);
    }

    /**
     * تغيير حالة الترشيح للكورس
     */
    public function toggleFeatured(AdvancedCourse $advancedCourse)
    {
        $advancedCourse->update([
            'is_featured' => !$advancedCourse->is_featured
        ]);

        $status = $advancedCourse->is_featured ? 'تم ترشيح' : 'تم إلغاء ترشيح';

        return response()->json([
            'success' => true,
            'message' => $status . ' الكورس بنجاح',
            'is_featured' => $advancedCourse->is_featured
        ]);
    }

    /**
     * عرض الطلبات الخاصة بالكورس
     */
    public function orders(AdvancedCourse $advancedCourse)
    {
        $orders = $advancedCourse->orders()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.advanced-courses.orders', compact('advancedCourse', 'orders'));
    }

    /**
     * تقرير الدروس وإجابات الطلاب على أسئلة الفيديو
     */
    public function lessonAnswers(AdvancedCourse $advancedCourse)
    {
        $course = $advancedCourse;
        $lessons = $course->lessons()
            ->ordered()
            ->with([
                'videoQuestions' => function ($q) {
                    $q->orderBy('time_seconds')->with(['question', 'answers' => function ($q) {
                        $q->with('user')->orderBy('created_at', 'desc');
                    }]);
                },
            ])
            ->get();

        return view('admin.advanced-courses.lesson-answers', compact('course', 'lessons'));
    }

    /**
     * إحصائيات الكورس
     */
    public function statistics(AdvancedCourse $advancedCourse)
    {
        $stats = [
            // إحصائيات الطلاب
            'students' => [
                'total' => $advancedCourse->enrollments->count(),
                'active' => $advancedCourse->enrollments->where('status', 'active')->count(),
                'completed' => $advancedCourse->enrollments->where('status', 'completed')->count(),
                'pending' => $advancedCourse->enrollments->where('status', 'pending')->count(),
            ],
            
            // إحصائيات الدروس
            'lessons' => [
                'total' => $advancedCourse->lessons->count(),
                'active' => $advancedCourse->lessons->where('is_active', true)->count(),
                'video' => $advancedCourse->lessons->where('type', 'video')->count(),
                'document' => $advancedCourse->lessons->where('type', 'document')->count(),
                'quiz' => $advancedCourse->lessons->where('type', 'quiz')->count(),
                'total_duration' => $advancedCourse->lessons->sum('duration_minutes'),
            ],
            
            // إحصائيات الطلبات
            'orders' => [
                'total' => $advancedCourse->orders->count(),
                'pending' => $advancedCourse->orders->where('status', 'pending')->count(),
                'approved' => $advancedCourse->orders->where('status', 'approved')->count(),
                'rejected' => $advancedCourse->orders->where('status', 'rejected')->count(),
            ],
            
            // التقدم العام
            'progress' => [
                'average' => $advancedCourse->enrollments->where('status', 'active')->avg('progress') ?? 0,
                'completion_rate' => $advancedCourse->enrollments->count() > 0 
                    ? ($advancedCourse->enrollments->where('status', 'completed')->count() / $advancedCourse->enrollments->count()) * 100 
                    : 0,
            ]
        ];

        return view('admin.advanced-courses.statistics', compact('advancedCourse', 'stats'));
    }

    /**
     * تصدير بيانات الكورس
     */
    public function export(AdvancedCourse $advancedCourse)
    {
        // يمكن تطوير هذه الوظيفة لتصدير بيانات الكورس إلى Excel أو PDF
        return response()->json([
            'message' => 'سيتم تطوير وظيفة التصدير قريباً'
        ]);
    }

    /**
     * نسخ الكورس
     */
    public function duplicate(AdvancedCourse $advancedCourse)
    {
        $newCourse = $advancedCourse->replicate();
        $newCourse->title = $advancedCourse->title . ' - نسخة';
        $newCourse->is_active = false;
        $newCourse->is_featured = false;
        $newCourse->save();

        // نسخ الدروس
        foreach ($advancedCourse->lessons as $lesson) {
            $newLesson = $lesson->replicate();
            $newLesson->advanced_course_id = $newCourse->id;
            $newLesson->save();
        }

        return redirect()->route('admin.advanced-courses.edit', $newCourse)
            ->with('success', 'تم نسخ الكورس بنجاح. يمكنك الآن تعديل البيانات حسب الحاجة.');
    }
}

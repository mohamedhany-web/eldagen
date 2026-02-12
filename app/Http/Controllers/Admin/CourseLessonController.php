<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseLessonController extends Controller
{
    /**
     * استخراج مسار الملف تحت القرص public من قيمة path (نسبي أو URL قديم)
     */
    private function attachmentStoragePath(array $attachment): string
    {
        $path = $attachment['path'] ?? '';
        if (str_contains($path, '/storage/')) {
            return (string) preg_replace('#.*/storage/#', '', $path);
        }
        return ltrim(str_replace('\\', '/', $path), '/');
    }

    /**
     * عرض دروس الكورس مع فلترة حسب القسم
     */
    public function index(Request $request, AdvancedCourse $course)
    {
        $sections = $course->sections()->ordered()->get();

        $query = $course->lessons()->with('section')->ordered();

        if ($request->filled('section_id')) {
            if ($request->section_id === 'none') {
                $query->whereNull('course_section_id');
            } else {
                $query->where('course_section_id', $request->section_id);
            }
        }

        $lessons = $query->get();

        return view('admin.course-lessons.index', compact('course', 'lessons', 'sections'));
    }

    /**
     * عرض صفحة إضافة درس جديد
     */
    public function create(AdvancedCourse $course)
    {
        $sections = $course->sections()->ordered()->get();
        return view('admin.course-lessons.create', compact('course', 'sections'));
    }

    /**
     * حفظ درس جديد
     */
    public function store(Request $request, AdvancedCourse $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,document,quiz,assignment',
            'content' => 'nullable|string',
            'course_section_id' => 'nullable|exists:course_sections,id',
            'video_url' => 'nullable|url',
            'video_source' => 'nullable|in:youtube,vimeo,google_drive,direct,other',

            'attachments.*' => 'nullable|file|max:10240', // 10MB per file
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_free' => 'boolean',
            'is_active' => 'boolean',
            'allow_flexible_submission' => 'boolean',
        ], [
            'title.required' => 'عنوان الدرس مطلوب',
            'title.max' => 'عنوان الدرس لا يجب أن يتجاوز 255 حرف',
            'type.required' => 'نوع الدرس مطلوب',
            'type.in' => 'نوع الدرس غير صحيح',
            'video_url.url' => 'رابط الفيديو غير صحيح',

            'attachments.*.max' => 'حجم المرفق لا يجب أن يتجاوز 10MB',
            'duration_minutes.min' => 'مدة الدرس لا يمكن أن تكون سالبة',
        ]);

        $data = $request->all();
        $data['advanced_course_id'] = $course->id;
        $data['is_free'] = $request->has('is_free');
        $data['is_active'] = $request->has('is_active');
        $data['allow_flexible_submission'] = $request->has('allow_flexible_submission');

        // تحديد ترتيب الدرس إذا لم يتم تحديده
        if (!isset($data['order'])) {
            $data['order'] = $course->lessons()->max('order') + 1;
        }
        
        // تحديد مدة الدرس الافتراضية
        if (!isset($data['duration_minutes']) || $data['duration_minutes'] === null || $data['duration_minutes'] === '') {
            $data['duration_minutes'] = 0;
        }

        $data['course_section_id'] = $request->input('course_section_id') ?: null;

        // التحقق من صحة رابط الفيديو وتحديد المصدر
        if ($data['type'] === 'video' && !empty($data['video_url'])) {
            if (!\App\Helpers\VideoHelper::isValidVideoUrl($data['video_url'])) {
                return back()->withErrors(['video_url' => 'رابط الفيديو غير صحيح أو غير مدعوم'])->withInput();
            }
            if (empty($data['video_source'])) {
                $data['video_source'] = \App\Helpers\VideoHelper::getVideoSource($data['video_url']);
            }
        }

        // رفع المرفقات (المسار نسبي لتسهيل الحذف والعرض)
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('course-attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
            $data['attachments'] = json_encode($attachments);
        }

        $lesson = CourseLesson::create($data);

        // إشعار تلقائي لطلاب الكورس: تم إضافة درس جديد
        \App\Models\Notification::sendToCourseStudents($course->id, [
            'sender_id' => auth()->id(),
            'title' => 'درس جديد في الكورس',
            'message' => 'تم إضافة درس: «' . $lesson->title . '» في كورس «' . $course->title . '». يمكنك مشاهدته من كورساتي.',
            'type' => 'course',
            'priority' => 'normal',
            'target_type' => 'course_students',
            'target_id' => $course->id,
            'action_url' => route('my-courses.show', $course),
            'action_text' => 'فتح الكورس',
        ]);

        return redirect()->route('admin.courses.lessons.index', $course)
            ->with('success', 'تم إضافة الدرس بنجاح');
    }

    /**
     * عرض تفاصيل الدرس
     */
    public function show(AdvancedCourse $course, CourseLesson $lesson)
    {
        return view('admin.course-lessons.show', compact('course', 'lesson'));
    }

    /**
     * عرض صفحة تعديل الدرس
     */
    public function edit(AdvancedCourse $course, CourseLesson $lesson)
    {
        if ($lesson->advanced_course_id != $course->id) {
            abort(404, 'الدرس غير تابع لهذا الكورس');
        }
        $sections = $course->sections()->ordered()->get();
        return view('admin.course-lessons.edit', compact('course', 'lesson', 'sections'));
    }

    /**
     * تحديث بيانات الدرس
     */
    public function update(Request $request, AdvancedCourse $course, CourseLesson $lesson)
    {
        if ($lesson->advanced_course_id != $course->id) {
            abort(404, 'الدرس غير تابع لهذا الكورس');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,document,quiz,assignment',
            'content' => 'nullable|string',
            'course_section_id' => 'nullable|exists:course_sections,id',
            'video_url' => 'nullable|url',
            'video_source' => 'nullable|in:youtube,vimeo,google_drive,direct,other',

            'attachments.*' => 'nullable|file|max:10240',
            'duration_minutes' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_free' => 'boolean',
            'is_active' => 'boolean',
            'allow_flexible_submission' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_free'] = $request->has('is_free');
        $data['is_active'] = $request->has('is_active');
        $data['allow_flexible_submission'] = $request->has('allow_flexible_submission');
        $data['course_section_id'] = $request->input('course_section_id') ?: null;

        // التحقق من صحة رابط الفيديو وتحديد المصدر
        if ($data['type'] === 'video' && !empty($data['video_url'])) {
            if (!\App\Helpers\VideoHelper::isValidVideoUrl($data['video_url'])) {
                return back()->withErrors(['video_url' => 'رابط الفيديو غير صحيح أو غير مدعوم'])->withInput();
            }
            if (empty($data['video_source'])) {
                $data['video_source'] = \App\Helpers\VideoHelper::getVideoSource($data['video_url']);
            }
        }

        // دمج المرفقات الجديدة مع الحالية (المسار نسبي) — إن لم يُرفع ملف جديد لا نغيّر المرفقات الحالية
        if ($request->hasFile('attachments')) {
            $currentAttachments = $lesson->getAttachmentsArray();
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('course-attachments', 'public');
                $currentAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
            $data['attachments'] = json_encode($currentAttachments);
        } else {
            unset($data['attachments']);
        }

        // إزالة مفاتيح الطلب التي لا تُخزَّن في الجدول
        unset($data['_token'], $data['_method']);

        $lesson->update($data);

        // إشعار تلقائي لطلاب الكورس: تم تحديث الدرس
        \App\Models\Notification::sendToCourseStudents($course->id, [
            'sender_id' => auth()->id(),
            'title' => 'تم تحديث درس في الكورس',
            'message' => 'تم تحديث درس: «' . $lesson->title . '» في كورس «' . $course->title . '». يمكنك مراجعته من كورساتي.',
            'type' => 'course',
            'priority' => 'normal',
            'target_type' => 'course_students',
            'target_id' => $course->id,
            'action_url' => route('my-courses.show', $course),
            'action_text' => 'فتح الكورس',
        ]);

        return redirect()->route('admin.courses.lessons.index', $course)
            ->with('success', 'تم تحديث الدرس بنجاح');
    }

    /**
     * حذف الدرس
     */
    public function destroy(AdvancedCourse $course, CourseLesson $lesson)
    {
        // حذف المرفقات من التخزين
        if ($lesson->attachments) {
            $attachments = json_decode($lesson->attachments, true);
            foreach ($attachments as $attachment) {
                $storagePath = $this->attachmentStoragePath($attachment);
                if ($storagePath && Storage::disk('public')->exists($storagePath)) {
                    Storage::disk('public')->delete($storagePath);
                }
            }
        }

        // حذف تقدم الطلاب في هذا الدرس
        \App\Models\LessonProgress::where('course_lesson_id', $lesson->id)->delete();

        $lesson->delete();

        return redirect()->route('admin.courses.lessons.index', $course)
            ->with('success', 'تم حذف الدرس بنجاح');
    }

    /**
     * حذف مرفق واحد من درس
     */
    public function removeAttachment(Request $request, AdvancedCourse $course, CourseLesson $lesson)
    {
        $request->validate(['index' => 'required|integer|min:0']);
        $index = (int) $request->input('index');
        $attachments = $lesson->getAttachmentsArray();
        if (!isset($attachments[$index])) {
            return back()->with('error', 'المرفق غير موجود.');
        }
        $attachment = $attachments[$index];
        $storagePath = $this->attachmentStoragePath($attachment);
        if ($storagePath && Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);
        }
        array_splice($attachments, $index, 1);
        $lesson->update(['attachments' => count($attachments) ? json_encode($attachments) : null]);
        return back()->with('success', 'تم حذف المرفق.');
    }

    /**
     * صفحة عرض مرفقات الدرس فقط (مع زر التعديل)
     */
    public function attachments(AdvancedCourse $course, CourseLesson $lesson)
    {
        return view('admin.course-lessons.attachments', compact('course', 'lesson'));
    }

    /**
     * إعادة ترتيب الدروس
     */
    public function reorder(Request $request, AdvancedCourse $course)
    {
        $request->validate([
            'lessons' => 'required|array',
            'lessons.*.id' => 'required|exists:course_lessons,id',
            'lessons.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->lessons as $lessonData) {
            CourseLesson::where('id', $lessonData['id'])
                ->where('advanced_course_id', $course->id)
                ->update(['order' => $lessonData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة ترتيب الدروس بنجاح'
        ]);
    }

    /**
     * تغيير حالة الدرس (تفعيل/إلغاء تفعيل)
     */
    public function toggleStatus(AdvancedCourse $course, CourseLesson $lesson)
    {
        $lesson->update([
            'is_active' => !$lesson->is_active
        ]);

        $status = $lesson->is_active ? 'تم تفعيل' : 'تم إلغاء تفعيل';

        return response()->json([
            'success' => true,
            'message' => $status . ' الدرس بنجاح',
            'is_active' => $lesson->is_active
        ]);
    }
}

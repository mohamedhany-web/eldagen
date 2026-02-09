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
     * عرض دروس الكورس
     */
    public function index(AdvancedCourse $course)
    {
        $lessons = $course->lessons()->ordered()->get();
        
        return view('admin.course-lessons.index', compact('course', 'lessons'));
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

        // رفع المرفقات
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('course-attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => Storage::url($path),
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
        $sections = $course->sections()->ordered()->get();
        return view('admin.course-lessons.edit', compact('course', 'lesson', 'sections'));
    }

    /**
     * تحديث بيانات الدرس
     */
    public function update(Request $request, AdvancedCourse $course, CourseLesson $lesson)
    {
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

        // رفع المرفقات الجديدة
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('course-attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => Storage::url($path),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
            $data['attachments'] = json_encode($attachments);
        }

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
        // حذف المرفقات فقط (الفيديوهات روابط خارجية)
        if ($lesson->attachments) {
            $attachments = json_decode($lesson->attachments, true);
            foreach ($attachments as $attachment) {
                if (Storage::disk('public')->exists(str_replace('/storage/', '', $attachment['path']))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $attachment['path']));
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

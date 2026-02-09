<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use Illuminate\Http\Request;

class CourseSectionController extends Controller
{
    public function index(AdvancedCourse $course)
    {
        $sections = $course->sections()->ordered()->withCount('lessons')->get();
        return view('admin.course-sections.index', compact('course', 'sections'));
    }

    public function create(AdvancedCourse $course)
    {
        return view('admin.course-sections.create', compact('course'));
    }

    public function store(Request $request, AdvancedCourse $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'title.required' => 'عنوان القسم مطلوب',
        ]);

        $order = $request->input('order') ?? ($course->sections()->max('order') + 1);
        $course->sections()->create([
            'title' => $request->title,
            'order' => $order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.courses.sections.index', $course)
            ->with('success', 'تم إضافة القسم بنجاح');
    }

    public function edit(AdvancedCourse $course, CourseSection $section)
    {
        if ($section->advanced_course_id != $course->id) {
            abort(404);
        }
        return view('admin.course-sections.edit', compact('course', 'section'));
    }

    public function update(Request $request, AdvancedCourse $course, CourseSection $section)
    {
        if ($section->advanced_course_id != $course->id) {
            abort(404);
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $section->update([
            'title' => $request->title,
            'order' => $request->input('order', $section->order),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.courses.sections.index', $course)
            ->with('success', 'تم تحديث القسم بنجاح');
    }

    public function destroy(AdvancedCourse $course, CourseSection $section)
    {
        if ($section->advanced_course_id != $course->id) {
            abort(404);
        }
        // إزالة الربط من الدروس دون حذف الدروس
        $section->lessons()->update(['course_section_id' => null]);
        $section->delete();

        return redirect()->route('admin.courses.sections.index', $course)
            ->with('success', 'تم حذف القسم بنجاح');
    }

    public function reorder(Request $request, AdvancedCourse $course)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:course_sections,id',
            'sections.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->sections as $item) {
            CourseSection::where('id', $item['id'])
                ->where('advanced_course_id', $course->id)
                ->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true, 'message' => 'تم حفظ الترتيب']);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicSubjectController extends Controller
{
    public function index()
    {
        $subjects = AcademicSubject::with(['academicYear'])
            ->withCount(['advancedCourses'])
            ->orderBy('academic_year_id')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('admin.academic-subjects.index', compact('subjects'));
    }

    public function create()
    {
        $academicYears = AcademicYear::where('is_active', true)->orderBy('order')->get();
        return view('admin.academic-subjects.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('academic_subjects')->where(function ($query) use ($request) {
                    return $query->where('academic_year_id', $request->academic_year_id);
                })
            ],
            'description' => 'nullable|string',
            'icon' => 'required|string|max:100',
            'color' => 'required|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'academic_year_id.required' => 'السنة الدراسية مطلوبة',
            'academic_year_id.exists' => 'السنة الدراسية المحددة غير موجودة',
            'name.required' => 'اسم المادة مطلوب',
            'name.max' => 'اسم المادة لا يجب أن يتجاوز 255 حرف',
            'code.required' => 'رمز المادة مطلوب',
            'code.unique' => 'رمز المادة موجود مسبقاً في هذه السنة الدراسية',
            'code.max' => 'رمز المادة لا يجب أن يتجاوز 100 حرف',
            'icon.required' => 'أيقونة المادة مطلوبة',
            'color.required' => 'لون المادة مطلوب',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? 0;

        AcademicSubject::create($data);

        return redirect()->route('admin.academic-subjects.index')
            ->with('success', 'تم إضافة المادة الدراسية بنجاح');
    }

    public function show(AcademicSubject $academicSubject)
    {
        $academicSubject->load([
            'academicYear',
            'advancedCourses' => function($query) {
                $query->with(['activations.user', 'lessons']);
            }
        ]);

        return view('admin.academic-subjects.show', compact('academicSubject'));
    }

    public function edit(AcademicSubject $academicSubject)
    {
        $academicYears = AcademicYear::where('is_active', true)->orderBy('order')->get();
        return view('admin.academic-subjects.edit', compact('academicSubject', 'academicYears'));
    }

    public function update(Request $request, AcademicSubject $academicSubject)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('academic_subjects')->where(function ($query) use ($request) {
                    return $query->where('academic_year_id', $request->academic_year_id);
                })->ignore($academicSubject->id)
            ],
            'description' => 'nullable|string',
            'icon' => 'required|string|max:100',
            'color' => 'required|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'academic_year_id.required' => 'السنة الدراسية مطلوبة',
            'academic_year_id.exists' => 'السنة الدراسية المحددة غير موجودة',
            'name.required' => 'اسم المادة مطلوب',
            'name.max' => 'اسم المادة لا يجب أن يتجاوز 255 حرف',
            'code.required' => 'رمز المادة مطلوب',
            'code.unique' => 'رمز المادة موجود مسبقاً في هذه السنة الدراسية',
            'code.max' => 'رمز المادة لا يجب أن يتجاوز 100 حرف',
            'icon.required' => 'أيقونة المادة مطلوبة',
            'color.required' => 'لون المادة مطلوب',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? 0;

        $academicSubject->update($data);

        return redirect()->route('admin.academic-subjects.index')
            ->with('success', 'تم تحديث المادة الدراسية بنجاح');
    }

    public function destroy(AcademicSubject $academicSubject)
    {
        if ($academicSubject->advancedCourses()->count() > 0) {
            return redirect()->route('admin.academic-subjects.index')
                ->with('error', 'لا يمكن حذف المادة لأنها تحتوي على كورسات');
        }

        $academicSubject->delete();

        return redirect()->route('admin.academic-subjects.index')
            ->with('success', 'تم حذف المادة الدراسية بنجاح');
    }

    public function toggleStatus(AcademicSubject $academicSubject)
    {
        $academicSubject->update([
            'is_active' => !$academicSubject->is_active
        ]);

        $status = $academicSubject->is_active ? 'تم تفعيل' : 'تم إلغاء تفعيل';

        return response()->json([
            'success' => true,
            'message' => $status . ' المادة الدراسية بنجاح',
            'is_active' => $academicSubject->is_active
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:academic_subjects,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            AcademicSubject::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة ترتيب المواد الدراسية بنجاح'
        ]);
    }
}
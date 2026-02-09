<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::withCount(['academicSubjects', 'advancedCourses'])
            ->ordered()
            ->get();
        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:academic_years',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم السنة الدراسية مطلوب',
            'name.unique' => 'اسم السنة الدراسية موجود مسبقاً',
        ]);

        $data = $request->only(['name', 'description', 'order', 'icon', 'color']);
        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? 0;
        $data['code'] = $this->generateUniqueCode();
        $data['start_date'] = $data['start_date'] ?? Carbon::now()->startOfYear()->format('Y-m-d');
        $data['end_date'] = $data['end_date'] ?? Carbon::now()->endOfYear()->format('Y-m-d');

        AcademicYear::create($data);
        Cache::forget('home_academic_years');

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'تم إضافة السنة الدراسية بنجاح');
    }

    public function show(AcademicYear $academicYear)
    {
        $academicYear->load(['academicSubjects.advancedCourses']);
        return view('admin.academic-years.show', compact('academicYear'));
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('academic_years')->ignore($academicYear->id),
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('academic_years')->ignore($academicYear->id),
            ],
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'name.required' => 'اسم السنة الدراسية مطلوب',
            'name.unique' => 'اسم السنة الدراسية موجود مسبقاً',
            'code.required' => 'رمز السنة الدراسية مطلوب',
            'code.unique' => 'رمز السنة الدراسية موجود مسبقاً',
        ]);

        $data = $request->only($academicYear->getFillable());
        $data['is_active'] = $request->has('is_active');
        $data['order'] = (int) ($data['order'] ?? $academicYear->order ?? 0);
        if (isset($data['start_date']) && $data['start_date'] === '') {
            $data['start_date'] = $academicYear->start_date ?? now()->startOfYear()->format('Y-m-d');
        }
        if (isset($data['end_date']) && $data['end_date'] === '') {
            $data['end_date'] = $academicYear->end_date ?? now()->endOfYear()->format('Y-m-d');
        }

        $academicYear->update($data);
        Cache::forget('home_academic_years');

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'تم تحديث السنة الدراسية بنجاح');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->academicSubjects()->count() > 0) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', 'لا يمكن حذف السنة الدراسية لأنها تحتوي على مواد دراسية');
        }

        if ($academicYear->advancedCourses()->count() > 0) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', 'لا يمكن حذف السنة الدراسية لأنها تحتوي على كورسات');
        }

        $academicYear->delete();
        Cache::forget('home_academic_years');

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'تم حذف السنة الدراسية بنجاح');
    }

    public function toggleStatus(AcademicYear $academicYear)
    {
        $academicYear->update([
            'is_active' => !$academicYear->is_active
        ]);
        Cache::forget('home_academic_years');

        $status = $academicYear->is_active ? 'تم تفعيل' : 'تم إلغاء تفعيل';

        return response()->json([
            'success' => true,
            'message' => $status . ' السنة الدراسية بنجاح',
            'is_active' => $academicYear->is_active
        ]);
    }

    public function reorder(Request $request)
    
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:academic_years,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            AcademicYear::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث ترتيب السنوات الدراسية بنجاح'
        ]);
    }

    /**
     * توليد رمز فريد للسنة الدراسية (AY1, AY2, ...)
     */
    private function generateUniqueCode(): string
    {
        $nextId = (int) AcademicYear::max('id') + 1;
        $code = 'AY' . $nextId;
        while (AcademicYear::where('code', $code)->exists()) {
            $nextId++;
            $code = 'AY' . $nextId;
        }
        return $code;
    }
}
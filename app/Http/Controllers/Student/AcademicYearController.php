<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * عرض السنوات الدراسية للطلاب
     */
    public function index()
    {
        $academicYears = AcademicYear::where('is_active', true)
            ->withCount('academicSubjects')
            ->orderBy('order')
            ->get();

        return view('student.academic-years.index', compact('academicYears'));
    }

    /**
     * عرض المواد الدراسية لسنة معينة
     */
    public function subjects(AcademicYear $academicYear)
    {
        $subjects = AcademicSubject::where('academic_year_id', $academicYear->id)
            ->where('is_active', true)
            ->withCount('advancedCourses')
            ->orderBy('order')
            ->get();

        return view('student.academic-years.subjects', compact('academicYear', 'subjects'));
    }
}
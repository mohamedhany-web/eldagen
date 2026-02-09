<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AdvancedCourse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * عرض الكورسات لمادة معينة
     */
    public function courses(AcademicSubject $academicSubject)
    {
        $courses = AdvancedCourse::where('academic_subject_id', $academicSubject->id)
            ->where('is_active', true)
            ->with(['academicYear', 'academicSubject'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.subjects.courses', compact('academicSubject', 'courses'));
    }
}
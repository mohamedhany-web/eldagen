<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Order;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * عرض تفاصيل الكورس
     */
    public function show(AdvancedCourse $advancedCourse)
    {
        $advancedCourse->load(['academicYear', 'academicSubject']);
        
        // التحقق من وجود طلب سابق للطالب
        $existingOrder = Order::where('user_id', auth()->id())
            ->where('advanced_course_id', $advancedCourse->id)
            ->latest()
            ->first();

        // التحقق من التسجيل في الكورس (جدول student_course_enrollments)
        $isEnrolled = auth()->user()->courseEnrollments()
            ->where('advanced_course_id', $advancedCourse->id)
            ->where('status', 'active')
            ->exists();

        return view('catalog.course-order', compact('advancedCourse', 'existingOrder', 'isEnrolled'));
    }
}
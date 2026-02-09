<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use App\Models\AdvancedCourse;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * كتالوج الكورسات العام (خارج المنصة) - بدون تسجيل دخول للتصفح
 */
class CatalogController extends Controller
{
    /**
     * صفحة الكورسات: سايدبار (سنوات → مواد) + بطاقات الكورسات
     */
    public function index(Request $request)
    {
        $years = AcademicYear::where('is_active', true)
            ->with(['academicSubjects' => fn ($q) => $q->where('is_active', true)->orderBy('order')])
            ->orderBy('order')
            ->get();

        $yearId = $request->get('year_id');
        $subjectId = $request->get('subject_id');
        $openYearId = $yearId;
        if (!$openYearId && $subjectId) {
            $openYearId = AcademicSubject::find($subjectId)?->academic_year_id;
        }

        $coursesQuery = AdvancedCourse::where('is_active', true)
            ->with(['academicYear', 'academicSubject']);

        if ($subjectId) {
            $coursesQuery->where('academic_subject_id', $subjectId);
        } elseif ($yearId) {
            $coursesQuery->where('academic_year_id', $yearId);
        }

        $courses = $coursesQuery->orderBy('created_at', 'desc')->paginate(12);

        $response = response()->view('catalog.index', compact('years', 'courses', 'yearId', 'subjectId', 'openYearId'));
        $response->header('Cache-Control', 'public, max-age=' . (config('performance.browser_cache_seconds.catalog', 300)));
        return $response;
    }

    /**
     * صفحة تفاصيل الكورس (عامة) + زر شراء الآن
     */
    public function show(AdvancedCourse $advancedCourse)
    {
        if (!$advancedCourse->is_active) {
            abort(404);
        }

        $advancedCourse->load(['academicYear', 'academicSubject', 'teacher']);

        $existingOrder = null;
        $isEnrolled = false;
        if (auth()->check()) {
            $existingOrder = Order::where('user_id', auth()->id())
                ->where('advanced_course_id', $advancedCourse->id)
                ->latest()
                ->first();
            $isEnrolled = auth()->user()->courseEnrollments()
                ->where('advanced_course_id', $advancedCourse->id)
                ->where('status', 'active')
                ->exists();
        }

        $relatedCourses = AdvancedCourse::where('is_active', true)
            ->where('id', '!=', $advancedCourse->id)
            ->where(function ($q) use ($advancedCourse) {
                $q->where('academic_subject_id', $advancedCourse->academic_subject_id)
                  ->orWhere('academic_year_id', $advancedCourse->academic_year_id);
            })
            ->with(['academicYear', 'academicSubject'])
            ->orderByRaw('academic_subject_id = ? DESC', [$advancedCourse->academic_subject_id])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        $response = response()->view('catalog.show', compact('advancedCourse', 'existingOrder', 'isEnrolled', 'relatedCourses'));
        $response->header('Cache-Control', 'public, max-age=' . (config('performance.browser_cache_seconds.catalog', 300)));
        return $response;
    }
}

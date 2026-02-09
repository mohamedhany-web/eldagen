<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LessonProgress;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class ParentReportController extends Controller
{
    /**
     * تطبيع رقم الجوال للبحث (01xxxxxxxxx)
     */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            return '0' . $digits;
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '01')) {
            return $digits;
        }
        return $phone;
    }

    /**
     * عرض صفحة ولي الأمر: نموذج إدخال الرقم أو التقارير بعد التحقق
     */
    public function index(Request $request)
    {
        $phone = session('parent_report_phone');

        if (!$phone) {
            return view('parent-report.index');
        }

        $students = User::where('role', 'student')
            ->where('parent_phone', $phone)
            ->with([
                'academicYear',
                'courseEnrollments.course.academicSubject',
                'courseEnrollments.course.academicYear',
                'examAttempts.exam.course',
            ])
            ->orderBy('name')
            ->get();

        if ($students->isEmpty()) {
            session()->forget('parent_report_phone');
            return redirect()->route('parent-report.index')->with('error', 'لا يوجد طالب مرتبط بهذا الرقم حالياً.');
        }

        $reports = [];
        foreach ($students as $user) {
            $enrollments = $user->courseEnrollments()->with(['course.academicSubject', 'course.academicYear'])
                ->orderBy('enrolled_at', 'desc')->get();
            $examAttempts = $user->examAttempts()->with(['exam.course'])->orderBy('started_at', 'desc')->get();
            $lessonProgressCount = LessonProgress::where('user_id', $user->id)->count();
            $lessonCompletedCount = LessonProgress::where('user_id', $user->id)->where('is_completed', true)->count();
            $orders = \App\Models\Order::where('user_id', $user->id)->with('course')->orderBy('created_at', 'desc')->get();
            $lessonProgressDetails = LessonProgress::where('user_id', $user->id)
                ->with(['lesson.course'])
                ->orderBy('completed_at', 'desc')
                ->limit(50)
                ->get();
            $recentActivity = ActivityLog::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(30)
                ->get();

            $reports[] = [
                'user' => $user,
                'enrollments' => $enrollments,
                'examAttempts' => $examAttempts,
                'lessonProgressCount' => $lessonProgressCount,
                'lessonCompletedCount' => $lessonCompletedCount,
                'orders' => $orders,
                'lessonProgressDetails' => $lessonProgressDetails,
                'recentActivity' => $recentActivity,
            ];
        }

        return view('parent-report.report', [
            'phone' => $phone,
            'students' => $students,
            'reports' => $reports,
        ]);
    }

    /**
     * التحقق من رقم ولي الأمر وعرض التقارير
     */
    public function submit(Request $request)
    {
        $raw = $request->input('phone', '');
        $phone = $this->normalizePhone($raw);
        if (!preg_match('/^01[0-9]{9}$/', $phone)) {
            return back()->withErrors(['phone' => 'رقم الجوال يجب أن يبدأ بـ 01 ويتكون من 11 رقماً.'])->withInput();
        }

        $exists = User::where('role', 'student')->where('parent_phone', $phone)->exists();
        if (!$exists) {
            return back()->withErrors(['phone' => 'لا يوجد طالب مسجّل بهذا الرقم كجوال ولي الأمر.'])->withInput();
        }

        session(['parent_report_phone' => $phone]);

        return redirect()->route('parent-report.index');
    }

    /**
     * تسجيل الخروج من تقرير ولي الأمر (مسح الجلسة)
     */
    public function clearSession()
    {
        session()->forget('parent_report_phone');
        return redirect()->route('parent-report.index')->with('success', 'تم الخروج. يمكنك إدخال رقم آخر.');
    }

    /**
     * تحميل التقرير كملف PDF منسق
     */
    public function downloadPdf()
    {
        $phone = session('parent_report_phone');
        if (!$phone) {
            return redirect()->route('parent-report.index')->with('error', 'يجب إدخال رقم الجوال أولاً.');
        }

        $students = User::where('role', 'student')
            ->where('parent_phone', $phone)
            ->with([
                'academicYear',
                'courseEnrollments.course.academicSubject',
                'courseEnrollments.course.academicYear',
                'examAttempts.exam.course',
            ])
            ->orderBy('name')
            ->get();

        if ($students->isEmpty()) {
            session()->forget('parent_report_phone');
            return redirect()->route('parent-report.index')->with('error', 'لا يوجد طالب مرتبط بهذا الرقم حالياً.');
        }

        $reports = [];
        foreach ($students as $user) {
            $enrollments = $user->courseEnrollments()->with(['course.academicSubject', 'course.academicYear'])
                ->orderBy('enrolled_at', 'desc')->get();
            $examAttempts = $user->examAttempts()->with(['exam.course'])->orderBy('started_at', 'desc')->get();
            $lessonProgressCount = LessonProgress::where('user_id', $user->id)->count();
            $lessonCompletedCount = LessonProgress::where('user_id', $user->id)->where('is_completed', true)->count();
            $orders = \App\Models\Order::where('user_id', $user->id)->with('course')->orderBy('created_at', 'desc')->get();
            $lessonProgressDetails = LessonProgress::where('user_id', $user->id)
                ->with(['lesson.course'])
                ->orderBy('completed_at', 'desc')
                ->limit(50)
                ->get();

            $reports[] = [
                'user' => $user,
                'enrollments' => $enrollments,
                'examAttempts' => $examAttempts,
                'lessonProgressCount' => $lessonProgressCount,
                'lessonCompletedCount' => $lessonCompletedCount,
                'orders' => $orders,
                'lessonProgressDetails' => $lessonProgressDetails,
            ];
        }

        $html = view('parent-report.pdf', [
            'phone' => $phone,
            'reports' => $reports,
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'xbriyaz',
            'margin_right' => 18,
            'margin_left' => 18,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'autoArabic' => true,
            'autoScriptToLang' => true,
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->SetTitle(config('seo.site_name', 'المنصة') . ' - تقرير ولي الأمر');
        $mpdf->WriteHTML($html);

        $filename = 'parent_report_' . date('Y-m-d_His') . '.pdf';
        return response()->streamDownload(function () use ($mpdf, $filename) {
            echo $mpdf->Output($filename, Destination::STRING_RETURN);
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

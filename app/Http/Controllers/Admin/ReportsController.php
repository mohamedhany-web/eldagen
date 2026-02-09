<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentCourseEnrollment;
use App\Models\ExamAttempt;
use App\Models\LessonProgress;
use App\Models\AdvancedCourse;
use App\Models\Order;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    /**
     * صفحة التقارير الرئيسية: إحصائيات عامة + قائمة الطلاب
     */
    public function index(Request $request)
    {
        // إحصائيات عامة
        $totalStudents = User::where('role', 'student')->count();
        $activeStudents = User::where('role', 'student')->where('is_active', true)->count();
        $suspendedStudents = User::where('role', 'student')->whereNotNull('suspended_at')->count();
        $totalEnrollments = StudentCourseEnrollment::count();
        $activeEnrollments = StudentCourseEnrollment::where('status', 'active')->count();
        $totalExamAttempts = ExamAttempt::count();
        $totalLessonProgress = LessonProgress::count();

        // قائمة الطلاب مع بياناتهم (بحث + ترقيم)
        $query = User::where('role', 'student')
            ->withCount(['courseEnrollments', 'examAttempts'])
            ->with(['academicYear']);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)->whereNull('suspended_at');
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'suspended') {
                $query->whereNotNull('suspended_at');
            }
        }

        $students = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.reports.index', compact(
            'totalStudents',
            'activeStudents',
            'suspendedStudents',
            'totalEnrollments',
            'activeEnrollments',
            'totalExamAttempts',
            'totalLessonProgress',
            'students'
        ));
    }

    /**
     * تقرير تفصيلي لطالب واحد: كل بيانات الطالب
     */
    public function student(User $user)
    {
        if ($user->role !== 'student') {
            return redirect()->route('admin.messages.index')->with('error', 'المستخدم ليس طالباً.');
        }

        $user->load([
            'academicYear',
            'courseEnrollments.course.academicSubject',
            'courseEnrollments.course.academicYear',
            'examAttempts.exam',
            'examAttempts.exam.course',
        ]);

        $enrollments = $user->courseEnrollments()->with(['course.academicSubject', 'course.academicYear'])->orderBy('enrolled_at', 'desc')->get();
        $examAttempts = $user->examAttempts()->with(['exam.course'])->orderBy('started_at', 'desc')->get();
        $lessonProgressCount = LessonProgress::where('user_id', $user->id)->count();
        $lessonCompletedCount = LessonProgress::where('user_id', $user->id)->where('is_completed', true)->count();
        $orders = Order::where('user_id', $user->id)->with('course')->orderBy('created_at', 'desc')->get();

        return view('admin.reports.student', compact(
            'user',
            'enrollments',
            'examAttempts',
            'lessonProgressCount',
            'lessonCompletedCount',
            'orders'
        ));
    }

    /**
     * تصدير تقرير الطالب كملف Excel منظم
     */
    public function exportStudentExcel(User $user): StreamedResponse|RedirectResponse
    {
        if ($user->role !== 'student') {
            return redirect()->route('admin.messages.index')->with('error', 'المستخدم ليس طالباً.');
        }

        $user->load([
            'academicYear',
            'courseEnrollments.course.academicSubject',
            'courseEnrollments.course.academicYear',
            'examAttempts.exam',
            'examAttempts.exam.course',
        ]);
        $enrollments = $user->courseEnrollments()->with(['course.academicSubject', 'course.academicYear'])->orderBy('enrolled_at', 'desc')->get();
        $examAttempts = $user->examAttempts()->with(['exam.course'])->orderBy('started_at', 'desc')->get();
        $orders = Order::where('user_id', $user->id)->with('course')->orderBy('created_at', 'desc')->get();
        $lessonProgressCount = LessonProgress::where('user_id', $user->id)->count();
        $lessonCompletedCount = LessonProgress::where('user_id', $user->id)->where('is_completed', true)->count();

        $spreadsheet = new Spreadsheet();
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $headerFontColor = ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]];
        $thinBorder = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]];

        // ورقة 1: البيانات الأساسية
        $sheet0 = $spreadsheet->getActiveSheet();
        $sheet0->setTitle('البيانات الأساسية');
        $basicData = [
            ['البيانات الأساسية للطالب', ''],
            ['الاسم', $user->name],
            ['البريد الإلكتروني', $user->email],
            ['الجوال', $user->phone ?? '—'],
            ['جوال ولي الأمر', $user->parent_phone ?? '—'],
            ['السنة الدراسية', $user->academicYear->name ?? '—'],
            ['تاريخ الميلاد', $user->birth_date ? $user->birth_date->format('Y-m-d') : '—'],
            ['آخر دخول', $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : '—'],
            ['الحالة', $user->suspended_at ? 'موقوف' : ($user->is_active ? 'نشط' : 'غير نشط')],
            ['عدد التسجيلات', $enrollments->count()],
            ['عدد محاولات الامتحانات', $examAttempts->count()],
            ['سجلات تقدم الدروس', $lessonProgressCount],
            ['دروس مكتملة', $lessonCompletedCount],
        ];
        $sheet0->fromArray($basicData, null, 'A1');
        $sheet0->mergeCells('A1:B1');
        $sheet0->getStyle('A1')->applyFromArray(array_merge($headerStyle, $headerFontColor));
        $sheet0->getStyle('A2:B' . (count($basicData) + 1))->applyFromArray($thinBorder);
        $sheet0->getColumnDimension('A')->setWidth(22);
        $sheet0->getColumnDimension('B')->setWidth(35);

        // ورقة 2: تسجيلات الكورسات
        $sheet1 = $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'التسجيلات'));
        $sheet1->setCellValue('A1', 'الكورس');
        $sheet1->setCellValue('B1', 'المادة / السنة');
        $sheet1->setCellValue('C1', 'الحالة');
        $sheet1->setCellValue('D1', 'التقدم %');
        $sheet1->setCellValue('E1', 'تاريخ التسجيل');
        $sheet1->setCellValue('F1', 'تاريخ التفعيل');
        $sheet1->getStyle('A1:F1')->applyFromArray(array_merge($headerStyle, $headerFontColor));
        $row = 2;
        foreach ($enrollments as $e) {
            $sheet1->setCellValue('A' . $row, $e->course->title ?? '—');
            $sheet1->setCellValue('B' . $row, ($e->course->academicSubject->name ?? '—') . ' / ' . ($e->course->academicYear->name ?? '—'));
            $sheet1->setCellValue('C' . $row, $e->status_text);
            $sheet1->setCellValue('D' . $row, $e->progress ?? 0);
            $sheet1->setCellValue('E' . $row, $e->enrolled_at ? $e->enrolled_at->format('Y-m-d') : '—');
            $sheet1->setCellValue('F' . $row, $e->activated_at ? $e->activated_at->format('Y-m-d') : '—');
            $row++;
        }
        $sheet1->getStyle('A1:F' . max($row, 2))->applyFromArray($thinBorder);
        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }

        // ورقة 3: محاولات الامتحانات
        $sheet2 = $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'الامتحانات'));
        $sheet2->setCellValue('A1', 'الامتحان');
        $sheet2->setCellValue('B1', 'الكورس');
        $sheet2->setCellValue('C1', 'الدرجة');
        $sheet2->setCellValue('D1', 'النسبة %');
        $sheet2->setCellValue('E1', 'الحالة');
        $sheet2->setCellValue('F1', 'تاريخ البدء');
        $sheet2->setCellValue('G1', 'تاريخ التسليم');
        $sheet2->getStyle('A1:G1')->applyFromArray(array_merge($headerStyle, $headerFontColor));
        $row = 2;
        foreach ($examAttempts as $a) {
            $sheet2->setCellValue('A' . $row, $a->exam->title ?? '—');
            $sheet2->setCellValue('B' . $row, $a->exam->course->title ?? '—');
            $sheet2->setCellValue('C' . $row, $a->score ?? '—');
            $sheet2->setCellValue('D' . $row, $a->percentage ? round($a->percentage, 1) : '—');
            $sheet2->setCellValue('E' . $row, $a->status ?? '—');
            $sheet2->setCellValue('F' . $row, $a->started_at ? $a->started_at->format('Y-m-d H:i') : '—');
            $sheet2->setCellValue('G' . $row, $a->submitted_at ? $a->submitted_at->format('Y-m-d H:i') : '—');
            $row++;
        }
        $sheet2->getStyle('A1:G' . max($row, 2))->applyFromArray($thinBorder);
        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // ورقة 4: الطلبات
        $sheet3 = $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'الطلبات'));
        $sheet3->setCellValue('A1', 'رقم الطلب');
        $sheet3->setCellValue('B1', 'الكورس');
        $sheet3->setCellValue('C1', 'المبلغ');
        $sheet3->setCellValue('D1', 'الحالة');
        $sheet3->setCellValue('E1', 'التاريخ');
        $sheet3->getStyle('A1:E1')->applyFromArray(array_merge($headerStyle, $headerFontColor));
        $row = 2;
        foreach ($orders as $o) {
            $sheet3->setCellValue('A' . $row, $o->id);
            $sheet3->setCellValue('B' . $row, $o->course->title ?? '—');
            $sheet3->setCellValue('C' . $row, $o->amount ?? '—');
            $sheet3->setCellValue('D' . $row, $o->status ?? '—');
            $sheet3->setCellValue('E' . $row, $o->created_at ? $o->created_at->format('Y-m-d H:i') : '—');
            $row++;
        }
        $sheet3->getStyle('A1:E' . max($row, 2))->applyFromArray($thinBorder);
        foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
            $sheet3->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'تقرير_طالب_' . preg_replace('/[^\p{L}\p{N}\-_]/u', '_', $user->name) . '_' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

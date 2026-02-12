<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseActivationCode;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivationCodeController extends Controller
{
    /**
     * الصفحة الرئيسية: عرض الكورسات التي لها أكواد تفعيل مع إحصائيات، وتوليد أكواد جديدة
     */
    public function index(Request $request)
    {
        $coursesWithCodes = AdvancedCourse::query()
            ->whereHas('activationCodes')
            ->with(['academicYear', 'academicSubject'])
            ->withCount('activationCodes')
            ->withCount(['activationCodes as active_codes_count' => fn($q) => $q->where('status', CourseActivationCode::STATUS_ACTIVE)])
            ->withCount(['activationCodes as used_codes_count' => fn($q) => $q->where('status', CourseActivationCode::STATUS_USED)])
            ->orderBy('title')
            ->get();

        $allCourses = AdvancedCourse::where('is_active', true)
            ->with(['academicYear', 'academicSubject'])
            ->orderBy('title')
            ->get();

        $stats = [
            'total' => CourseActivationCode::count(),
            'active' => CourseActivationCode::active()->count(),
            'used' => CourseActivationCode::used()->count(),
        ];

        return view('admin.activation-codes.index', compact('coursesWithCodes', 'allCourses', 'stats'));
    }

    /**
     * صفحة خاصة بكورس واحد: عرض أكواد هذا الكورس فقط مع إمكانية التصدير Excel
     */
    public function show(Request $request, AdvancedCourse $advancedCourse)
    {
        $query = $advancedCourse->activationCodes()->with(['creator', 'usedBy', 'order']);

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'used') {
                $query->used();
            }
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'like', "%{$search}%")
                ->orWhereHas('usedBy', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
        }

        $codes = $query->orderBy('created_at', 'desc')->paginate(25);

        $stats = [
            'total' => $advancedCourse->activationCodes()->count(),
            'active' => $advancedCourse->activationCodes()->active()->count(),
            'used' => $advancedCourse->activationCodes()->used()->count(),
        ];

        return view('admin.activation-codes.show', compact('advancedCourse', 'codes', 'stats'));
    }

    /**
     * توليد مجموعة من الأكواد
     */
    public function store(Request $request)
    {
        $request->validate([
            'advanced_course_id' => 'required|exists:advanced_courses,id',
            'quantity' => 'required|integer|min:1|max:500',
        ], [
            'advanced_course_id.required' => 'اختر الكورس',
            'quantity.required' => 'عدد الأكواد مطلوب',
            'quantity.min' => 'الحد الأدنى 1',
            'quantity.max' => 'الحد الأقصى 500',
        ]);

        $course = AdvancedCourse::findOrFail($request->advanced_course_id);
        $quantity = (int) $request->quantity;
        $created = [];

        for ($i = 0; $i < $quantity; $i++) {
            $code = CourseActivationCode::create([
                'code' => CourseActivationCode::generateUniqueCode(10),
                'advanced_course_id' => $course->id,
                'created_by' => auth()->id(),
                'status' => CourseActivationCode::STATUS_ACTIVE,
            ]);
            $created[] = $code->code;
        }

        $message = "تم توليد {$quantity} كود بنجاح للكورس: {$course->title}";
        if ($quantity <= 20) {
            $message .= ' — الأكواد: ' . implode(', ', $created);
        } else {
            $message .= ' — أول 5: ' . implode(', ', array_slice($created, 0, 5)) . ' ...';
        }

        return redirect()->route('admin.activation-codes.show', $course)->with('success', $message);
    }

    /**
     * تصدير الأكواد إلى ملف Excel منظم
     */
    public function export(Request $request): StreamedResponse
    {
        $query = CourseActivationCode::with(['course.academicSubject', 'course.academicYear', 'creator', 'usedBy', 'order']);

        if ($request->filled('course')) {
            $query->where('advanced_course_id', $request->course);
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'used') {
                $query->used();
            }
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'like', "%{$search}%")
                ->orWhereHas('usedBy', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
        }

        $codes = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('أكواد التفعيل');

        // اتجاه من اليمين لليسار
        $sheet->setRightToLeft(true);

        // عنوان التقرير (صف 1)
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'تقرير أكواد التفعيل - منصة الطارق');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4F46E5');
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getRowDimension(1)->setRowHeight(28);

        // تاريخ التصدير (صف 2)
        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'تاريخ التصدير: ' . now()->translatedFormat('l d F Y - H:i'));
        $sheet->getStyle('A2')->getFont()->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getFont()->getColor()->setRGB('6B7280');
        $sheet->getRowDimension(2)->setRowHeight(20);

        // رأس الجدول (صف 4)
        $headers = ['م', 'الكود', 'الكورس', 'الحالة', 'اسم الطالب', 'هاتف الطالب', 'تاريخ الاستخدام', 'تاريخ الإنشاء'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '4', $h);
            $col++;
        }
        $sheet->getStyle('A4:H4')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A4:H4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('6366F1');
        $sheet->getStyle('A4:H4')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A4:H4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getRowDimension(4)->setRowHeight(24);

        // البيانات
        $row = 5;
        $n = 1;
        foreach ($codes as $code) {
            $sheet->setCellValue('A' . $row, $n);
            $sheet->setCellValue('B' . $row, $code->code);
            $sheet->setCellValue('C' . $row, $code->course ? $code->course->title : '—');
            $sheet->setCellValue('D' . $row, $code->status === 'used' ? 'مستخدم' : 'متاح');
            $sheet->setCellValue('E' . $row, $code->usedBy ? $code->usedBy->name : '—');
            $sheet->setCellValue('F' . $row, $code->usedBy && $code->usedBy->phone ? $code->usedBy->phone : '—');
            $sheet->setCellValue('G' . $row, $code->used_at ? $code->used_at->format('Y-m-d H:i') : '—');
            $sheet->setCellValue('H' . $row, $code->created_at->format('Y-m-d H:i'));

            $fillColor = $row % 2 === 1 ? 'F9FAFB' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':H' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($fillColor);
            $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('D' . $row)->getFont()->getColor()->setRGB($code->status === 'used' ? 'B45309' : '047857');
            $row++;
            $n++;
        }

        // عرض الأعمدة تلقائياً
        foreach (range('A', 'H') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(25);

        // تجميد الصف الرابع (رأس الجدول)
        $sheet->freezePane('A5');

        // إطار حول كل الخلايا المستخدمة
        $lastRow = max(4, $row - 1);
        $sheet->getStyle('A4:H' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $fileName = 'activation-codes-' . now()->format('Y-m-d-His') . '.xlsx';

        return $this->streamExcelResponse($spreadsheet, $fileName);
    }

    /**
     * تصدير أكواد كورس واحد إلى Excel
     */
    public function exportForCourse(AdvancedCourse $advancedCourse): StreamedResponse
    {
        $query = $advancedCourse->activationCodes()->with(['creator', 'usedBy', 'order']);
        $codes = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('أكواد التفعيل - ' . \Str::limit($advancedCourse->title, 25));

        $sheet->setRightToLeft(true);

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'أكواد التفعيل — ' . $advancedCourse->title);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4F46E5');
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'تاريخ التصدير: ' . now()->translatedFormat('l d F Y - H:i'));
        $sheet->getStyle('A2')->getFont()->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getFont()->getColor()->setRGB('6B7280');
        $sheet->getRowDimension(2)->setRowHeight(20);

        $headers = ['م', 'الكود', 'الحالة', 'اسم الطالب', 'هاتف الطالب', 'تاريخ الاستخدام', 'تاريخ الإنشاء', 'ملاحظات'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '4', $h);
            $col++;
        }
        $sheet->getStyle('A4:H4')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A4:H4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('6366F1');
        $sheet->getStyle('A4:H4')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A4:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A4:H4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getRowDimension(4)->setRowHeight(24);

        $row = 5;
        $n = 1;
        foreach ($codes as $code) {
            $sheet->setCellValue('A' . $row, $n);
            $sheet->setCellValue('B' . $row, $code->code);
            $sheet->setCellValue('C' . $row, $code->status === 'used' ? 'مستخدم' : 'متاح');
            $sheet->setCellValue('D' . $row, $code->usedBy ? $code->usedBy->name : '—');
            $sheet->setCellValue('E' . $row, $code->usedBy && $code->usedBy->phone ? $code->usedBy->phone : '—');
            $sheet->setCellValue('F' . $row, $code->used_at ? $code->used_at->format('Y-m-d H:i') : '—');
            $sheet->setCellValue('G' . $row, $code->created_at->format('Y-m-d H:i'));
            $sheet->setCellValue('H' . $row, '');
            $fillColor = $row % 2 === 1 ? 'F9FAFB' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':H' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($fillColor);
            $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('C' . $row)->getFont()->getColor()->setRGB($code->status === 'used' ? 'B45309' : '047857');
            $row++;
            $n++;
        }

        foreach (range('A', 'H') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
        $sheet->freezePane('A5');
        $lastRow = max(4, $row - 1);
        $sheet->getStyle('A4:H' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $safeTitle = \Str::slug($advancedCourse->title);
        $fileName = 'activation-codes-' . $safeTitle . '-' . now()->format('Y-m-d-His') . '.xlsx';

        return $this->streamExcelResponse($spreadsheet, $fileName);
    }

    private function streamExcelResponse(Spreadsheet $spreadsheet, string $fileName): StreamedResponse
    {
        return new StreamedResponse(function () use ($spreadsheet) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}

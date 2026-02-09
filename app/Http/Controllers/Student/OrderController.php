<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseActivationCode;
use App\Models\Order;
use App\Models\StudentCourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * عرض طلبات الطالب
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['course.academicSubject', 'course.academicYear', 'activationCode'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.orders.index', compact('orders'));
    }

    /**
     * إنشاء طلب جديد (تحويل/نقدي/أخرى أو كود التفعيل)
     */
    public function store(Request $request, AdvancedCourse $advancedCourse)
    {
        $isCodePayment = $request->payment_method === 'code';

        $rules = [
            'payment_method' => 'required|in:bank_transfer,cash,other,code',
            'notes' => 'nullable|string|max:500',
        ];
        if ($isCodePayment) {
            $rules['activation_code'] = 'required|string|max:32';
        } else {
            $rules['payment_proof'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        }

        $request->validate($rules, [
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_proof.required' => 'صورة الإيصال مطلوبة',
            'activation_code.required' => 'كود التفعيل مطلوب',
            'payment_proof.image' => 'يجب أن يكون الملف صورة',
            'payment_proof.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png أو jpg',
            'payment_proof.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
        ]);

        // الدفع بالكود: يُسمح به حتى لو كان الحساب مسجلاً مسبقاً (إعادة التفعيل بالكود)
        if ($isCodePayment) {
            return $this->processCodePayment($request, $advancedCourse);
        }

        // للطرق الأخرى فقط: منع طلب جديد إذا كان مسجلاً أو لديه طلب معلق
        $existingApprovedOrder = Order::where('user_id', auth()->id())
            ->where('advanced_course_id', $advancedCourse->id)
            ->where('status', Order::STATUS_APPROVED)
            ->exists();

        if ($existingApprovedOrder) {
            return back()->with('error', 'أنت مسجل بالفعل في هذا الكورس');
        }

        $existingPendingOrder = Order::where('user_id', auth()->id())
            ->where('advanced_course_id', $advancedCourse->id)
            ->where('status', Order::STATUS_PENDING)
            ->exists();

        if ($existingPendingOrder) {
            return back()->with('error', 'لديك طلب في الانتظار لهذا الكورس');
        }

        $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

        Order::create([
            'user_id' => auth()->id(),
            'advanced_course_id' => $advancedCourse->id,
            'amount' => $advancedCourse->price ?? 0,
            'payment_method' => $request->payment_method,
            'payment_proof' => $paymentProofPath,
            'notes' => $request->notes,
            'status' => Order::STATUS_PENDING,
        ]);

        return back()->with('success', 'تم إرسال طلبك بنجاح! سيتم مراجعته قريباً');
    }

    /**
     * معالجة الطلب بدفع كود التفعيل: التحقق من الكود، إنشاء الطلب مقبول، تفعيل التسجيل، تحديث الكود
     */
    private function processCodePayment(Request $request, AdvancedCourse $advancedCourse)
    {
        $codeInput = strtoupper(trim($request->activation_code));

        $code = CourseActivationCode::where('code', $codeInput)
            ->where('advanced_course_id', $advancedCourse->id)
            ->active()
            ->first();

        if (!$code) {
            return back()->with('error', 'كود التفعيل غير صحيح أو مستخدم مسبقاً أو لا يخص هذا الكورس. تحقق من الكود وحاول مرة أخرى.');
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => auth()->id(),
                'advanced_course_id' => $advancedCourse->id,
                'amount' => 0,
                'payment_method' => 'code',
                'payment_proof' => null,
                'activation_code_id' => $code->id,
                'notes' => $request->notes,
                'status' => Order::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            $existingEnrollment = StudentCourseEnrollment::where('user_id', auth()->id())
                ->where('advanced_course_id', $advancedCourse->id)
                ->first();

            if (!$existingEnrollment) {
                StudentCourseEnrollment::create([
                    'user_id' => auth()->id(),
                    'advanced_course_id' => $advancedCourse->id,
                    'enrolled_at' => now(),
                    'activated_at' => now(),
                    'activated_by' => auth()->id(),
                    'status' => 'active',
                    'progress' => 0,
                ]);
            } else {
                $existingEnrollment->update([
                    'status' => 'active',
                    'activated_at' => now(),
                    'activated_by' => auth()->id(),
                ]);
            }

            $code->update([
                'status' => CourseActivationCode::STATUS_USED,
                'used_at' => now(),
                'used_by' => auth()->id(),
                'order_id' => $order->id,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تفعيل الكود. يرجى المحاولة لاحقاً.');
        }

        return redirect()->route('orders.show', $order)->with('success', 'تم تفعيل الكورس بنجاح! يمكنك الدخول إليه الآن.');
    }

    /**
     * عرض تفاصيل الطلب
     */
    public function show(Order $order)
    {
        // التأكد من أن الطلب يخص الطالب الحالي
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['course.academicSubject', 'course.academicYear', 'approver', 'activationCode']);

        return view('catalog.order-show', compact('order'));
    }
}
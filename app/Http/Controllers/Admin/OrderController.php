<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StudentCourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * عرض قائمة الطلبات
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'course.academicSubject', 'course.academicYear', 'activationCode']);

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب طريقة الدفع
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })->orWhereHas('course', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // إحصائيات سريعة
        $stats = [
            'total' => Order::count(),
            'pending' => Order::pending()->count(),
            'approved' => Order::approved()->count(),
            'rejected' => Order::rejected()->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * عرض تفاصيل الطلب
     */
    public function show(Order $order)
    {
        $order->load(['user', 'course.academicSubject', 'course.academicYear', 'approver', 'activationCode']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * الموافقة على الطلب
     */
    public function approve(Request $request, Order $order)
    {
        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'لا يمكن الموافقة على هذا الطلب');
        }

        try {
            DB::beginTransaction();

            // تحديث حالة الطلب
            $order->update([
                'status' => Order::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            // التحقق من وجود تسجيل مسبق
            $existingEnrollment = StudentCourseEnrollment::where('user_id', $order->user_id)
                ->where('advanced_course_id', $order->advanced_course_id)
                ->first();

            if (!$existingEnrollment) {
                // تسجيل الطالب في الكورس
                StudentCourseEnrollment::create([
                    'user_id' => $order->user_id,
                    'advanced_course_id' => $order->advanced_course_id,
                    'enrolled_at' => now(),
                    'activated_at' => now(),
                    'activated_by' => auth()->id(),
                    'status' => 'active',
                    'progress' => 0,
                ]);
            } else {
                // تفعيل التسجيل إذا كان موجود ولكن غير مفعل
                $existingEnrollment->update([
                    'status' => 'active',
                    'activated_at' => now(),
                    'activated_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return back()->with('success', 'تمت الموافقة على الطلب وتم تفعيل الكورس للطالب');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء معالجة الطلب');
        }
    }

    /**
     * رفض الطلب
     */
    public function reject(Request $request, Order $order)
    {
        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'لا يمكن رفض هذا الطلب');
        }

        $order->update([
            'status' => Order::STATUS_REJECTED,
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم رفض الطلب');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentSessionsController extends Controller
{
    /**
     * عرض قائمة تسجيلات دخول الطلاب (الجهاز المسموح لكل طالب)
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->where('role', 'student')
            ->with('studentDevice')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(15)->withQueryString();

        return view('admin.student-sessions.index', compact('students'));
    }

    /**
     * إلغاء تسجيل الدخول للطالب (حذف الجهاز المسجّل) ليتمكن من الدخول من جهاز آخر
     */
    public function revoke(User $user)
    {
        if ($user->role !== 'student') {
            return back()->with('error', 'هذا المستخدم ليس طالباً.');
        }

        DB::transaction(function () use ($user) {
            // حذف تسجيل الجهاز المسموح
            StudentDevice::where('user_id', $user->id)->delete();
            // إنهاء جلسته الحالية من جدول الجلسات (يُخرج من الجهاز الحالي)
            DB::table('sessions')->where('user_id', $user->id)->delete();
        });

        return back()->with('success', 'تم إلغاء تسجيل الدخول للطالب «' . $user->name . '». يمكنه الآن الدخول من جهاز جديد.');
    }
}

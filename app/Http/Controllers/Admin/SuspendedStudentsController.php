<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountViolation;
use App\Models\User;
use Illuminate\Http\Request;

class SuspendedStudentsController extends Controller
{
    /**
     * قائمة الطلاب الموقوفين (المخالفين)
     */
    public function index()
    {
        $students = User::query()
            ->whereNotNull('suspended_at')
            ->students()
            ->with(['accountViolations' => fn ($q) => $q->latest()->limit(5)])
            ->orderByDesc('suspended_at')
            ->paginate(20);

        return view('admin.suspended-students.index', compact('students'));
    }

    /**
     * إعادة تفعيل حساب طالب
     */
    public function reinstate(User $user)
    {
        if ($user->role !== 'student' || !$user->suspended_at) {
            return back()->with('error', 'الحساب غير موقوف أو ليس طالباً.');
        }

        $user->update([
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        // تسجيل إعادة التفعيل على آخر مخالفة (اختياري)
        AccountViolation::where('user_id', $user->id)
            ->whereNull('reinstated_at')
            ->update([
                'reinstated_at' => now(),
                'reinstated_by' => auth()->id(),
            ]);

        return back()->with('success', 'تم إعادة تفعيل حساب الطالب «' . $user->name . '» بنجاح.');
    }
}

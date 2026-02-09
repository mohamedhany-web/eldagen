<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    /**
     * عرض قائمة المستخدمين والتحكم في الصلاحيات (الدور والتفعيل)
     */
    public function index(Request $request)
    {
        $query = User::query()->orderBy('name');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.permissions.index', compact('users'));
    }

    /**
     * تحديث دور المستخدم أو حالة التفعيل
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,student',
            'is_active' => 'nullable|boolean',
        ]);

        $data = ['role' => $request->role];
        if ($request->has('is_active')) {
            $data['is_active'] = (bool) $request->is_active;
        }

        $user->update($data);

        return back()->with('success', 'تم تحديث صلاحيات «' . $user->name . '» بنجاح.');
    }
}

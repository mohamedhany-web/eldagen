<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MaintenanceController extends Controller
{
    /**
     * عرض صفحة الصيانة في لوحة الأدمن (تفعيل/إيقاف)
     */
    public function index()
    {
        $isMaintenance = Cache::get('maintenance_mode', false);
        $bypassPath = trim(config('maintenance.bypass_path', 'maint-admin-secure'), '/');
        $adminEntryUrl = rtrim(config('app.url'), '/') . '/' . $bypassPath;

        return view('admin.maintenance.index', compact('isMaintenance', 'adminEntryUrl'));
    }

    /**
     * تفعيل وضع الصيانة
     */
    public function enable()
    {
        Cache::put('maintenance_mode', true);

        return back()->with('success', 'تم تفعيل وضع الصيانة. الموقع متوقف للزوار، ولوحة الأدمن تعمل بشكل طبيعي.');
    }

    /**
     * إيقاف وضع الصيانة (تشغيل الموقع)
     */
    public function disable()
    {
        Cache::forget('maintenance_mode');

        return back()->with('success', 'تم تشغيل الموقع. جميع الصفحات والعمليات تعمل بشكل طبيعي.');
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * قيم افتراضية للإعدادات
     */
    private function defaultPreferences(): array
    {
        return [
            'theme' => 'auto',
            'language' => 'ar',
            'notify_courses' => true,
            'notify_orders' => true,
            'notify_exams' => true,
            'show_progress' => true,
            'show_activity' => false,
        ];
    }

    /**
     * عرض صفحة الإعدادات
     */
    public function index()
    {
        $user = auth()->user();
        $prefs = array_merge($this->defaultPreferences(), $user->preferences ?? []);

        return view('student.settings.index', [
            'preferences' => $prefs,
        ]);
    }

    /**
     * حفظ إعدادات الطالب
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'theme' => 'nullable|in:light,dark,auto',
            'language' => 'nullable|in:ar,en',
            'notify_courses' => 'nullable|boolean',
            'notify_orders' => 'nullable|boolean',
            'notify_exams' => 'nullable|boolean',
            'show_progress' => 'nullable|boolean',
            'show_activity' => 'nullable|boolean',
        ]);

        $current = array_merge($this->defaultPreferences(), $user->preferences ?? []);

        $current['theme'] = $request->input('theme', $current['theme']);
        $current['language'] = $request->input('language', $current['language']);
        $current['notify_courses'] = $request->boolean('notify_courses', $current['notify_courses']);
        $current['notify_orders'] = $request->boolean('notify_orders', $current['notify_orders']);
        $current['notify_exams'] = $request->boolean('notify_exams', $current['notify_exams']);
        $current['show_progress'] = $request->boolean('show_progress', $current['show_progress']);
        $current['show_activity'] = $request->boolean('show_activity', $current['show_activity']);

        $user->update(['preferences' => $current]);

        return redirect()->route('settings')->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}

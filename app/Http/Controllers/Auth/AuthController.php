<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->filled('intended')) {
            session(['url.intended' => $request->intended]);
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'regex:/^01[0-9]{9}$/'],
            'password' => 'required|string',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.regex' => 'رقم الهاتف يجب أن يبدأ بـ 01 ويتكون من 11 رقماً (مثال: 01xxxxxxxxx)',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('phone', $request->phone)->first();

        // تقييد الطالب بجهاز واحد: إذا كان مسجلاً من جهاز آخر نرفض الدخول
        if ($user && $user->role === 'student' && $user->is_active) {
            if (Hash::check($request->password, $user->password)) {
                $device = StudentDevice::where('user_id', $user->id)->first();
                $currentSessionId = $request->session()->getId();
                if ($device && $device->session_id !== $currentSessionId) {
                    return back()->withErrors([
                        'phone' => 'حسابك مسجّل حالياً من جهاز واحد فقط. لفتح الحساب من جهاز آخر، تواصل مع الإدارة ليقوموا بإلغاء تسجيل الدخول من الجهاز الحالي.',
                    ])->withInput();
                }
            }
        }

        $credentials = $request->only('phone', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // حفظ جهاز الطالب المسموح له (جهاز واحد فقط) وظهوره في صفحة تسجيلات الطلاب
            if ($user->role === 'student') {
                StudentDevice::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'session_id' => $request->session()->getId(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'last_activity' => now(),
                    ]
                );
            }
            $user->updateLastLogin();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'phone' => 'بيانات الدخول غير صحيحة.',
        ])->withInput();
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^01[0-9]{9}$/', 'unique:users'],
            'parent_phone' => ['required', 'string', 'regex:/^01[0-9]{9}$/'],
            'email' => 'nullable|email|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'الاسم مطلوب',
            'phone.required' => 'رقم هاتف الطالب مطلوب',
            'phone.regex' => 'رقم الهاتف يجب أن يبدأ بـ 01 ويتكون من 11 رقماً (مثال: 01xxxxxxxxx)',
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً',
            'parent_phone.required' => 'رقم هاتف ولي الأمر مطلوب',
            'parent_phone.regex' => 'رقم هاتف ولي الأمر يجب أن يبدأ بـ 01 ويتكون من 11 رقماً',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'parent_phone' => $request->parent_phone,
            'email' => $request->input('email'),
            'password' => Hash::make($request->password),
            'role' => 'student',
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // تسجيل جهاز الطالب فوراً ليظهر في صفحة تسجيلات الطلاب (admin/student-sessions)
        StudentDevice::updateOrCreate(
            ['user_id' => $user->id],
            [
                'session_id' => $request->session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_activity' => now(),
            ]
        );
        $user->updateLastLogin();

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        // حذف جهاز الطالب المسجّل حتى يتمكن من الدخول مرة أخرى من نفس الجهاز أو غيره بعد تسجيل الخروج
        if ($user && $user->role === 'student') {
            StudentDevice::where('user_id', $user->id)->delete();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

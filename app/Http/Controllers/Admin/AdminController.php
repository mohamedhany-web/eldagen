<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DashboardStatsContract;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\QuestionBank;
use App\Models\ActivityLog;
use App\Models\VideoWatch;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct(
        protected DashboardStatsContract $dashboardStats
    ) {
        // التأكد من أن المستخدم هو إداري
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || !Auth::user()->isAdmin()) {
                return redirect('/dashboard')->with('error', 'غير مسموح لك بالوصول لهذه الصفحة');
            }
            return $next($request);
        });
    }

    /**
     * لوحة التحكم الرئيسية للإدارة (SOLID: تعتمد على العقد وليس التنفيذ)
     */
    public function dashboard()
    {
        $stats = $this->dashboardStats->getAdminStats();
        $monthlyStats = $this->dashboardStats->getAdminMonthlyStats();

        return view('admin.dashboard', compact('stats', 'monthlyStats'));
    }

    /**
     * إدارة المستخدمين
     */
    public function users(Request $request)
    {
        $query = User::query();

        // فلترة حسب الدور (فقط عند اختيار قيمة)
        $role = $request->query('role', $request->input('role', ''));
        if ($role !== '' && in_array($role, ['admin', 'student', 'teacher', 'parent'], true)) {
            $query->where('role', $role);
        }

        // فلترة حسب الحالة (فقط عند اختيار نشط أو غير نشط)
        $status = $request->query('status', $request->input('status', ''));
        if ($status === '1' || $status === '0') {
            $query->where('is_active', (int) $status);
        }

        // البحث: الاسم، البريد، الهاتف (من query string لطلبات GET)
        $search = trim((string) ($request->query('search') ?? $request->input('search', '')));
        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'LIKE', $like)
                  ->orWhere('email', 'LIKE', $like)
                  ->orWhereRaw('phone LIKE ?', [$like]);
            });
        }

        $users = $query->latest()->paginate(config('performance.pagination.users', 20))->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * إنشاء مستخدم جديد
     */
    public function createUser()
    {
        $permissionGroups = config('permissions.admin', []);
        return view('admin.users.create', compact('permissionGroups'));
    }

    /**
     * حفظ مستخدم جديد
     */
    public function storeUser(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,student',
            'is_active' => 'required|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ];
        if ($request->role === 'student') {
            $rules['parent_phone'] = 'required|string|regex:/^01[0-9]{9}$/';
        } else {
            $rules['parent_phone'] = 'nullable|string|regex:/^01[0-9]{9}$/';
        }
        $validator = Validator::make($request->all(), $rules, [
            'name.required' => 'الاسم مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مستخدم مسبقاً',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'role.required' => 'الدور مطلوب',
            'parent_phone.required' => 'جوال ولي الأمر إلزامي للطالب',
            'parent_phone.regex' => 'جوال ولي الأمر يجب أن يبدأ بـ 01 ويتكون من 11 رقماً',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->is_active,
        ];
        if ($request->filled('parent_phone')) {
            $data['parent_phone'] = $request->parent_phone;
        }
        if ($request->role === 'admin') {
            $allowed = array_merge(
                array_keys(config('permissions.admin.إدارة النظام', [])),
                array_keys(config('permissions.admin.إدارة المحتوى', [])),
                array_keys(config('permissions.admin.الرسائل والتقارير', []))
            );
            $data['permissions'] = array_values(array_intersect($request->input('permissions', []), $allowed));
        }
        $user = User::create($data);

        // تسجيل النشاط
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_created',
            'model_type' => 'User',
            'model_id' => $user->id,
            'new_values' => $user->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->dashboardStats->forgetAdminStatsCache();
        return redirect()->route('admin.users')->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * عرض صفحة تعديل المستخدم
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $permissionGroups = config('permissions.admin', []);
        return view('admin.users.edit', compact('user', 'permissionGroups'));
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldValues = $user->toArray();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'phone' => 'required|string|unique:users,phone,' . $id,
            'role' => 'required|in:admin,student',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => $request->role,
            'is_active' => $request->is_active,
        ];
        if ($request->has('email')) {
            $updateData['email'] = $request->email ?: null;
        }
        if ($request->role === 'admin') {
            $allowed = array_merge(
                array_keys(config('permissions.admin.إدارة النظام', [])),
                array_keys(config('permissions.admin.إدارة المحتوى', [])),
                array_keys(config('permissions.admin.الرسائل والتقارير', []))
            );
            $updateData['permissions'] = array_values(array_intersect($request->input('permissions', []), $allowed));
        } else {
            $updateData['permissions'] = null;
        }
        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }
        $user->update($updateData);

        // تسجيل النشاط
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_updated',
            'model_type' => 'User',
            'model_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $user->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->dashboardStats->forgetAdminStatsCache();
        return back()->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    /**
     * حذف مستخدم
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // منع حذف المدير الحالي
        if ($user->id === Auth::id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        $oldValues = $user->toArray();
        $user->delete();

        // تسجيل النشاط
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_deleted',
            'model_type' => 'User',
            'model_id' => $id,
            'old_values' => $oldValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->dashboardStats->forgetAdminStatsCache();
        return back()->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * إدارة الكورسات
     */
    public function courses()
    {
        $courses = Course::with(['subject', 'teacher', 'enrollments'])
                        ->withCount('enrollments')
                        ->latest()
                        ->paginate(config('performance.pagination.courses', 15));

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * تفعيل/إلغاء تفعيل كورس
     */
    public function toggleCourseStatus($id)
    {
        $course = Course::findOrFail($id);
        $oldStatus = $course->status;
        
        $course->update([
            'status' => $course->status === 'published' ? 'draft' : 'published'
        ]);

        // تسجيل النشاط
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'course_status_changed',
            'model_type' => 'Course',
            'model_id' => $course->id,
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $course->status],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'تم تغيير حالة الكورس بنجاح');
    }

    /**
     * عرض سجل النشاطات
     */
    public function activityLog(Request $request)
    {
        $query = ActivityLog::with('user');

        // فلترة حسب المستخدم
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // فلترة حسب النشاط
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // فلترة حسب التاريخ
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->latest()->paginate(config('performance.pagination.activity_log', 25));
        $users = User::select('id', 'name')->get();

        return view('admin.activity-log', compact('activities', 'users'));
    }

    /**
     * إحصائيات المنصة
     */
    public function statistics()
    {
        $stats = [
            'users_by_month' => User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                                  ->groupBy('year', 'month')
                                  ->orderBy('year', 'desc')
                                  ->orderBy('month', 'desc')
                                  ->take(12)
                                  ->get(),
            
            'courses_by_subject' => Course::join('subjects', 'courses.subject_id', '=', 'subjects.id')
                                         ->selectRaw('subjects.name, COUNT(*) as count')
                                         ->groupBy('subjects.id', 'subjects.name')
                                         ->get(),
            
            'exam_performance' => ExamAttempt::selectRaw('AVG(score) as avg_score, COUNT(*) as total_attempts')
                                           ->where('status', 'submitted')
                                           ->first(),
            
            'video_engagement' => VideoWatch::selectRaw('AVG(progress_percentage) as avg_progress, COUNT(*) as total_watches')
                                          ->first(),
        ];

        return view('admin.statistics', compact('stats'));
    }
}
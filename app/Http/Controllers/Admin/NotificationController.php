<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\AdvancedCourse;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * عرض قائمة الإشعارات المرسلة
     */
    public function index(Request $request)
    {
        $query = Notification::with(['user', 'sender'])
                            ->where('sender_id', Auth::id());

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->where('is_read', true);
            } elseif ($request->status === 'unread') {
                $query->where('is_read', false);
            }
        }

        // البحث
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        // إحصائيات
        $stats = [
            'total' => Notification::where('sender_id', Auth::id())->count(),
            'unread' => Notification::where('sender_id', Auth::id())->unread()->count(),
            'today' => Notification::where('sender_id', Auth::id())->whereDate('created_at', today())->count(),
            'by_type' => Notification::where('sender_id', Auth::id())
                                   ->selectRaw('type, count(*) as count')
                                   ->groupBy('type')
                                   ->pluck('count', 'type'),
        ];

        $notificationTypes = Notification::getTypes();

        return view('admin.notifications.index', compact('notifications', 'stats', 'notificationTypes'));
    }

    /**
     * عرض صفحة إرسال إشعار جديد
     */
    public function create()
    {
        $notificationTypes = Notification::getTypes();
        $priorities = Notification::getPriorities();
        $targetTypes = Notification::getTargetTypes();
        
        $academicYears = AcademicYear::active()->orderBy('order')->get();
        $academicSubjects = AcademicSubject::active()->orderBy('name')->get();
        $courses = AdvancedCourse::active()->with(['academicSubject'])->orderBy('title')->get();
        $students = User::where('role', 'student')->where('is_active', true)->orderBy('name')->get();

        return view('admin.notifications.create', compact(
            'notificationTypes', 'priorities', 'targetTypes', 
            'academicYears', 'academicSubjects', 'courses', 'students'
        ));
    }

    /**
     * إرسال إشعار جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:' . implode(',', array_keys(Notification::getTypes())),
            'priority' => 'required|in:' . implode(',', array_keys(Notification::getPriorities())),
            'target_type' => 'required|in:' . implode(',', array_keys(Notification::getTargetTypes())),
            'target_id' => 'nullable|integer',
            'action_url' => 'nullable|url',
            'action_text' => 'nullable|string|max:100',
            'expires_at' => 'nullable|date|after:now',
            'send_immediately' => 'boolean',
        ], [
            'title.required' => 'عنوان الإشعار مطلوب',
            'message.required' => 'نص الإشعار مطلوب',
            'type.required' => 'نوع الإشعار مطلوب',
            'priority.required' => 'أولوية الإشعار مطلوبة',
            'target_type.required' => 'نوع المستهدفين مطلوب',
        ]);

        $data = [
            'sender_id' => Auth::id(),
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'priority' => $request->priority,
            'target_type' => $request->target_type,
            'target_id' => $request->target_id,
            'action_url' => $request->action_url,
            'action_text' => $request->action_text,
            'expires_at' => $request->expires_at,
            'data' => $request->additional_data ? json_decode($request->additional_data, true) : null,
        ];

        // تحديد المستهدفين وإرسال الإشعارات
        $sentCount = $this->sendNotificationToTargets($request->target_type, $request->target_id, $data);

        return redirect()->route('admin.notifications.index')
            ->with('success', "تم إرسال الإشعار بنجاح إلى {$sentCount} طالب");
    }

    /**
     * عرض تفاصيل الإشعار
     */
    public function show(Notification $notification)
    {
        $notification->load(['user', 'sender']);
        
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * حذف الإشعار
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'تم حذف الإشعار بنجاح');
    }

    /**
     * إرسال الإشعارات للمستهدفين
     */
    private function sendNotificationToTargets($targetType, $targetId, $data)
    {
        switch ($targetType) {
            case 'all_students':
                Notification::sendToAllStudents($data);
                return User::where('role', 'student')->where('is_active', true)->count();

            case 'course_students':
                if ($targetId) {
                    Notification::sendToCourseStudents($targetId, $data);
                    return \App\Models\StudentCourseEnrollment::where('advanced_course_id', $targetId)
                                                             ->where('status', 'active')
                                                             ->count();
                }
                break;

            case 'year_students':
                if ($targetId) {
                    Notification::sendToYearStudents($targetId, $data);
                    $courseIds = AdvancedCourse::where('academic_year_id', $targetId)->pluck('id');
                    return \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                             ->where('status', 'active')
                                                             ->distinct('user_id')
                                                             ->count();
                }
                break;

            case 'subject_students':
                if ($targetId) {
                    Notification::sendToSubjectStudents($targetId, $data);
                    $courseIds = AdvancedCourse::where('academic_subject_id', $targetId)->pluck('id');
                    return \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                             ->where('status', 'active')
                                                             ->distinct('user_id')
                                                             ->count();
                }
                break;

            case 'individual':
                if ($targetId) {
                    Notification::sendToUser($targetId, $data);
                    return 1;
                }
                break;
        }

        return 0;
    }

    /**
     * إرسال إشعار سريع
     */
    public function quickSend(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_type' => 'required|string',
            'target_id' => 'nullable|integer',
        ]);

        $data = [
            'sender_id' => Auth::id(),
            'title' => $request->title,
            'message' => $request->message,
            'type' => 'general',
            'priority' => 'normal',
            'target_type' => $request->target_type,
            'target_id' => $request->target_id,
        ];

        $sentCount = $this->sendNotificationToTargets($request->target_type, $request->target_id, $data);

        return response()->json([
            'success' => true,
            'message' => "تم إرسال الإشعار إلى {$sentCount} طالب",
            'sent_count' => $sentCount,
        ]);
    }

    /**
     * الحصول على عدد المستهدفين
     */
    public function getTargetCount(Request $request)
    {
        $targetType = $request->target_type;
        $targetId = $request->target_id;

        $count = 0;

        switch ($targetType) {
            case 'all_students':
                $count = User::where('role', 'student')->where('is_active', true)->count();
                break;

            case 'course_students':
                if ($targetId) {
                    $count = \App\Models\StudentCourseEnrollment::where('advanced_course_id', $targetId)
                                                              ->where('status', 'active')
                                                              ->count();
                }
                break;

            case 'year_students':
                if ($targetId) {
                    $courseIds = AdvancedCourse::where('academic_year_id', $targetId)->pluck('id');
                    $count = \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                              ->where('status', 'active')
                                                              ->distinct('user_id')
                                                              ->count();
                }
                break;

            case 'subject_students':
                if ($targetId) {
                    $courseIds = AdvancedCourse::where('academic_subject_id', $targetId)->pluck('id');
                    $count = \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $courseIds)
                                                              ->where('status', 'active')
                                                              ->distinct('user_id')
                                                              ->count();
                }
                break;

            case 'individual':
                $count = 1;
                break;
        }

        return response()->json(['count' => $count]);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead(Request $request)
    {
        $query = Notification::where('sender_id', Auth::id())->unread();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $count = $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "تم تحديد {$count} إشعار كمقروء",
            'count' => $count,
        ]);
    }

    /**
     * حذف الإشعارات القديمة
     */
    public function cleanup(Request $request)
    {
        $days = $request->input('days', 30);
        
        $count = Notification::where('sender_id', Auth::id())
                            ->where('created_at', '<', now()->subDays($days))
                            ->where('is_read', true)
                            ->delete();

        return response()->json([
            'success' => true,
            'message' => "تم حذف {$count} إشعار قديم",
            'count' => $count,
        ]);
    }

    /**
     * إحصائيات الإشعارات
     */
    public function statistics()
    {
        $stats = [
            'overview' => [
                'total_sent' => Notification::where('sender_id', Auth::id())->count(),
                'total_read' => Notification::where('sender_id', Auth::id())->read()->count(),
                'total_unread' => Notification::where('sender_id', Auth::id())->unread()->count(),
                'today_sent' => Notification::where('sender_id', Auth::id())->whereDate('created_at', today())->count(),
            ],
            'by_type' => Notification::where('sender_id', Auth::id())
                                   ->selectRaw('type, count(*) as count')
                                   ->groupBy('type')
                                   ->get(),
            'by_priority' => Notification::where('sender_id', Auth::id())
                                       ->selectRaw('priority, count(*) as count')
                                       ->groupBy('priority')
                                       ->get(),
            'by_target' => Notification::where('sender_id', Auth::id())
                                     ->selectRaw('target_type, count(*) as count')
                                     ->groupBy('target_type')
                                     ->get(),
            'recent_activity' => Notification::where('sender_id', Auth::id())
                                            ->selectRaw('DATE(created_at) as date, count(*) as count')
                                            ->groupBy('date')
                                            ->orderBy('date', 'desc')
                                            ->take(7)
                                            ->get(),
        ];

        return view('admin.notifications.statistics', compact('stats'));
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * عرض الإشعارات للطالب
     */
    public function index(Request $request)
    {
        $query = Auth::user()->customNotifications()->with(['sender']);

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

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $notifications = $query->whereNull('expires_at')
                             ->orWhere('expires_at', '>', now())
                             ->orderBy('is_read', 'asc')
                             ->orderBy('priority', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->paginate(20);

        // إحصائيات
        $stats = [
            'total' => Auth::user()->customNotifications()->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->count(),
            'unread' => Auth::user()->customNotifications()->unread()->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->count(),
            'today' => Auth::user()->customNotifications()->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->whereDate('created_at', today())->count(),
            'urgent' => Auth::user()->customNotifications()->where('priority', 'urgent')->unread()->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->count(),
        ];

        $notificationTypes = Notification::getTypes();
        $priorities = Notification::getPriorities();

        return view('student.notifications.index', compact('notifications', 'stats', 'notificationTypes', 'priorities'));
    }

    /**
     * عرض تفاصيل الإشعار
     */
    public function show(Notification $notification)
    {
        // التحقق من الصلاحية
        if ($notification->user_id !== Auth::id()) {
            return redirect()->route('notifications')->with('error', 'غير مصرح لك بعرض هذا الإشعار');
        }

        // تحديد كمقروء
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        $notification->load(['sender']);

        return view('student.notifications.show', compact('notification'));
    }

    /**
     * تحديد الإشعار كمقروء
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        $count = Auth::user()
                    ->customNotifications()
                    ->unread()
                    ->update([
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
     * حذف الإشعار
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $notification->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الإشعار']);
    }

    /**
     * الحصول على عدد الإشعارات غير المقروءة
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->customNotifications()->unread()->where(function($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * الحصول على آخر الإشعارات
     */
    public function getRecent()
    {
        $notifications = Auth::user()
                           ->customNotifications()
                           ->with(['sender'])
                           ->where(function($q) {
                               $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                           })
                           ->orderBy('created_at', 'desc')
                           ->take(5)
                           ->get();

        return response()->json($notifications);
    }

    /**
     * الحصول على الإشعارات غير المقروءة لعرضها كـ popup (ريل تايم)
     */
    public function getUnreadForPopup()
    {
        $notifications = Auth::user()
                           ->customNotifications()
                           ->unread()
                           ->where(function($q) {
                               $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                           })
                           ->orderBy('created_at', 'desc')
                           ->take(10)
                           ->get(['id', 'title', 'message', 'type', 'priority', 'action_url', 'action_text', 'created_at']);

        return response()->json($notifications);
    }

    /**
     * حذف الإشعارات القديمة والمقروءة
     */
    public function cleanup()
    {
        $count = Auth::user()
                    ->customNotifications()
                    ->where('is_read', true)
                    ->where('created_at', '<', now()->subDays(30))
                    ->delete();

        return response()->json([
            'success' => true,
            'message' => "تم حذف {$count} إشعار قديم",
            'count' => $count,
        ]);
    }
}
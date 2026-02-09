@extends('layouts.app')

@section('title', 'الإشعارات')
@section('header', 'الإشعارات')

@section('content')
<div class="space-y-6">
    <!-- الهيدر والإحصائيات -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">آخر التحديثات والرسائل المهمة</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($stats['unread'] > 0)
                        <button onclick="markAllAsRead()" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-check ml-2"></i>
                            تحديد الكل كمقروء
                        </button>
                    @endif
                    <button onclick="cleanup()" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-broom ml-2"></i>
                        تنظيف
                    </button>
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600">{{ $stats['total'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">إجمالي الإشعارات</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-red-600">{{ $stats['unread'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">غير مقروءة</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['today'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">اليوم</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600">{{ $stats['urgent'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">عاجلة</div>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نوع الإشعار</label>
                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">جميع الأنواع</option>
                    @foreach($notificationTypes as $key => $type)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">جميع الحالات</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>غير مقروءة</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروءة</option>
                </select>
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الأولوية</label>
                <select name="priority" id="priority" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">جميع الأولويات</option>
                    @foreach($priorities as $key => $priority)
                        <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $priority }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-filter ml-2"></i>
                    فلترة
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة الإشعارات -->
    @if($notifications->count() > 0)
        <div class="space-y-4">
            @foreach($notifications as $notification)
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden
                {{ !$notification->is_read ? 'border-r-4 border-primary-500' : '' }}">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4 space-x-reverse flex-1">
                            <!-- أيقونة الإشعار -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center
                                    @if($notification->type_color == 'blue') bg-blue-100 dark:bg-blue-900
                                    @elseif($notification->type_color == 'green') bg-green-100 dark:bg-green-900
                                    @elseif($notification->type_color == 'yellow') bg-yellow-100 dark:bg-yellow-900
                                    @elseif($notification->type_color == 'red') bg-red-100 dark:bg-red-900
                                    @elseif($notification->type_color == 'purple') bg-purple-100 dark:bg-purple-900
                                    @elseif($notification->type_color == 'orange') bg-orange-100 dark:bg-orange-900
                                    @else bg-gray-100 dark:bg-gray-900
                                    @endif">
                                    <i class="{{ $notification->type_icon }} 
                                        @if($notification->type_color == 'blue') text-blue-600 dark:text-blue-400
                                        @elseif($notification->type_color == 'green') text-green-600 dark:text-green-400
                                        @elseif($notification->type_color == 'yellow') text-yellow-600 dark:text-yellow-400
                                        @elseif($notification->type_color == 'red') text-red-600 dark:text-red-400
                                        @elseif($notification->type_color == 'purple') text-purple-600 dark:text-purple-400
                                        @elseif($notification->type_color == 'orange') text-orange-600 dark:text-orange-400
                                        @else text-gray-600 dark:text-gray-400
                                        @endif"></i>
                                </div>
                            </div>
                            
                            <!-- محتوى الإشعار -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $notification->title }}</h3>
                                    
                                    @if($notification->priority !== 'normal')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($notification->priority_color == 'red') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($notification->priority_color == 'yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                            @endif">
                                            {{ $priorities[$notification->priority] ?? $notification->priority }}
                                        </span>
                                    @endif

                                    @if(!$notification->is_read)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            <i class="fas fa-circle text-xs ml-1"></i>
                                            جديد
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-gray-600 dark:text-gray-400 mb-3">{{ $notification->message }}</p>
                                
                                <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center">
                                        <i class="fas fa-user ml-1"></i>
                                        من: {{ $notification->sender->name ?? 'النظام' }}
                                    </span>
                                    
                                    <span class="flex items-center">
                                        <i class="fas fa-clock ml-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>

                                    @if($notification->expires_at)
                                        <span class="flex items-center">
                                            <i class="fas fa-hourglass-end ml-1"></i>
                                            ينتهي {{ $notification->expires_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>

                                @if($notification->action_url && $notification->action_text)
                                    <div class="mt-4">
                                        <a href="{{ $notification->action_url }}" 
                                           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            {{ $notification->action_text }}
                                            <i class="fas fa-external-link-alt mr-2"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- الإجراءات -->
                        <div class="flex items-center space-x-2 space-x-reverse ml-4">
                            @if(!$notification->is_read)
                                <button onclick="markAsRead({{ $notification->id }})" 
                                        class="text-green-600 hover:text-green-800 transition-colors p-2" 
                                        title="تحديد كمقروء">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif
                            
                            <a href="{{ route('notifications.show', $notification) }}" 
                               class="text-blue-600 hover:text-blue-800 transition-colors p-2" 
                               title="عرض التفاصيل">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <button onclick="deleteNotification({{ $notification->id }})" 
                                    class="text-red-600 hover:text-red-800 transition-colors p-2" 
                                    title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- التصفح -->
        <div class="mt-8">
            {{ $notifications->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-bell-slash text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد إشعارات</h3>
            <p class="text-gray-500 dark:text-gray-400">ستظهر هنا آخر التحديثات والرسائل المهمة</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function markAllAsRead() {
    if (confirm('هل تريد تحديد جميع الإشعارات كمقروءة؟')) {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function deleteNotification(notificationId) {
    if (confirm('هل تريد حذف هذا الإشعار؟')) {
        fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function cleanup() {
    if (confirm('هل تريد حذف الإشعارات المقروءة الأقدم من 30 يوم؟')) {
        fetch('/notifications/cleanup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>
@endpush
@endsection
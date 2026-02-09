@extends('layouts.app')

@section('title', 'إدارة الإشعارات')
@section('header', 'إدارة الإشعارات')

@section('content')
<div class="space-y-6">
    <!-- الهيدر والإحصائيات -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">إرسال وإدارة الإشعارات للطلاب</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.notifications.statistics') }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-chart-bar ml-2"></i>
                        الإحصائيات
                    </a>
                    <a href="{{ route('admin.notifications.create') }}" 
                       class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus ml-2"></i>
                        إرسال إشعار جديد
                    </a>
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
                    <div class="text-3xl font-bold text-green-600">{{ $stats['total'] - $stats['unread'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">مقروءة</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['today'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">اليوم</div>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="البحث في العنوان أو النص..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
            </div>

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
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروءة</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>غير مقروءة</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-search ml-2"></i>
                    بحث
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة الإشعارات -->
    @if($notifications->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الإشعارات المرسلة ({{ $notifications->total() }})</h3>
                    <div class="flex items-center gap-2">
                        <button onclick="markAllAsRead()" 
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors">
                            <i class="fas fa-check ml-1"></i>
                            تحديد الكل كمقروء
                        </button>
                        <button onclick="cleanupOld()" 
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors">
                            <i class="fas fa-trash ml-1"></i>
                            حذف القديمة
                        </button>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($notifications as $notification)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $notification->is_read ? 'opacity-75' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 space-x-reverse flex-1">
                                <!-- أيقونة النوع -->
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center
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
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $notification->title }}</h4>
                                        
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($notification->priority_color == 'red') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($notification->priority_color == 'yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($notification->priority_color == 'blue') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                            @endif">
                                            {{ \App\Models\Notification::getPriorities()[$notification->priority] ?? $notification->priority }}
                                        </span>

                                        @if(!$notification->is_read)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                جديد
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <p class="text-gray-600 dark:text-gray-400 mb-3">{{ Str::limit($notification->message, 150) }}</p>
                                    
                                    <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center">
                                            <i class="fas fa-user ml-1"></i>
                                            إلى: {{ $notification->user->name }}
                                        </span>
                                        
                                        <span class="flex items-center">
                                            <i class="fas fa-tag ml-1"></i>
                                            {{ \App\Models\Notification::getTypes()[$notification->type] ?? $notification->type }}
                                        </span>
                                        
                                        <span class="flex items-center">
                                            <i class="fas fa-clock ml-1"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>

                                        @if($notification->target_type !== 'individual')
                                            <span class="flex items-center">
                                                <i class="fas fa-users ml-1"></i>
                                                {{ \App\Models\Notification::getTargetTypes()[$notification->target_type] ?? $notification->target_type }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- الإجراءات -->
                            <div class="flex items-center space-x-2 space-x-reverse ml-4">
                                <a href="{{ route('admin.notifications.show', $notification) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors p-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition-colors p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- التصفح -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $notifications->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-bell text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد إشعارات</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">ابدأ بإرسال الإشعارات للطلاب</p>
            <a href="{{ route('admin.notifications.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-plus ml-2"></i>
                إرسال أول إشعار
            </a>
        </div>
    @endif

    <!-- إرسال سريع -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">إرسال سريع</h3>
        </div>
        <div class="p-6">
            <form id="quick-send-form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="quick_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">العنوان</label>
                    <input type="text" id="quick_title" name="title" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                           placeholder="عنوان الإشعار">
                </div>

                <div>
                    <label for="quick_target" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المستهدفين</label>
                    <select id="quick_target" name="target_type" required onchange="updateQuickTargetCount()"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">اختر المستهدفين</option>
                        <option value="all_students">جميع الطلاب</option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">سيتم الإرسال إلى: <span id="quick-target-count">0</span> طالب</p>
                </div>

                <div class="md:col-span-2">
                    <label for="quick_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">النص</label>
                    <textarea id="quick_message" name="message" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                              placeholder="اكتب نص الإشعار هنا..."></textarea>
                </div>

                <div class="md:col-span-2 flex items-center justify-end">
                    <button type="button" onclick="sendQuickNotification()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-paper-plane ml-2"></i>
                        إرسال سريع
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- إحصائيات حسب النوع -->
    @if($stats['by_type']->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">توزيع الإشعارات حسب النوع</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($stats['by_type'] as $type => $count)
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $count }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $notificationTypes[$type] ?? $type }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function updateQuickTargetCount() {
    const targetType = document.getElementById('quick_target').value;
    const targetId = null; // للإرسال السريع
    
    if (targetType) {
        fetch(`{{ route('admin.notifications.target-count') }}?target_type=${targetType}&target_id=${targetId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('quick-target-count').textContent = data.count;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    } else {
        document.getElementById('quick-target-count').textContent = '0';
    }
}

function sendQuickNotification() {
    const form = document.getElementById('quick-send-form');
    const formData = new FormData(form);
    
    const data = {
        title: formData.get('title'),
        message: formData.get('message'),
        target_type: formData.get('target_type'),
        target_id: null,
    };
    
    if (!data.title || !data.message || !data.target_type) {
        alert('يرجى ملء جميع الحقول المطلوبة');
        return;
    }
    
    fetch('{{ route("admin.notifications.quick-send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            form.reset();
            document.getElementById('quick-target-count').textContent = '0';
            location.reload();
        } else {
            alert('حدث خطأ في إرسال الإشعار');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في إرسال الإشعار');
    });
}

function markAllAsRead() {
    if (confirm('هل تريد تحديد جميع الإشعارات كمقروءة؟')) {
        fetch('{{ route("admin.notifications.mark-all-read") }}', {
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

function cleanupOld() {
    if (confirm('هل تريد حذف الإشعارات المقروءة الأقدم من 30 يوم؟')) {
        fetch('{{ route("admin.notifications.cleanup") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ days: 30 })
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

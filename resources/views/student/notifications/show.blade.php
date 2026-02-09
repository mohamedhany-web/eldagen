@extends('layouts.app')

@section('title', $notification->title)
@section('header', 'تفاصيل الإشعار')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('notifications') }}" class="hover:text-primary-600">الإشعارات</a>
                <span class="mx-2">/</span>
                <span>{{ $notification->title }}</span>
            </nav>
        </div>
        <a href="{{ route('notifications') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للإشعارات
        </a>
    </div>

    <!-- محتوى الإشعار -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- المحتوى الرئيسي -->
        <div class="xl:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <!-- هيدر الإشعار -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <!-- أيقونة النوع -->
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
                                @endif text-xl"></i>
                        </div>

                        <!-- العنوان والحالة -->
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $notification->title }}</h1>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($notification->type_color == 'blue') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($notification->type_color == 'green') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($notification->type_color == 'yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($notification->type_color == 'red') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @elseif($notification->type_color == 'purple') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                    @elseif($notification->type_color == 'orange') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                    @endif">
                                    {{ \App\Models\Notification::getTypes()[$notification->type] ?? $notification->type }}
                                </span>

                                @if($notification->priority !== 'normal')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($notification->priority_color == 'red') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @elseif($notification->priority_color == 'yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                        @endif">
                                        {{ \App\Models\Notification::getPriorities()[$notification->priority] ?? $notification->priority }}
                                    </span>
                                @endif

                                @if($notification->is_read)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                        <i class="fas fa-check ml-1"></i>
                                        مقروء
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <i class="fas fa-circle text-xs ml-1"></i>
                                        جديد
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- محتوى الإشعار -->
                <div class="p-6">
                    <div class="prose max-w-none dark:prose-invert">
                        <div class="text-gray-900 dark:text-white text-lg leading-relaxed whitespace-pre-wrap">{{ $notification->message }}</div>
                    </div>

                    <!-- زر الإجراء -->
                    @if($notification->action_url && $notification->action_text)
                        <div class="mt-8 p-6 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-primary-900 dark:text-primary-100 mb-1">إجراء مطلوب</h4>
                                    <p class="text-sm text-primary-700 dark:text-primary-300">انقر على الزر للمتابعة</p>
                                </div>
                                <a href="{{ $notification->action_url }}" 
                                   class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors"
                                   target="_blank">
                                    {{ $notification->action_text }}
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- بيانات إضافية -->
                    @if($notification->data)
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">معلومات إضافية</h4>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                @foreach($notification->data as $key => $value)
                                    <div class="flex items-center justify-between py-1">
                                        <span class="font-medium">{{ ucfirst($key) }}:</span>
                                        <span>{{ is_array($value) ? json_encode($value) : $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- معلومات الإشعار -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">معلومات الإشعار</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">المرسل</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $notification->sender->name ?? 'النظام' }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">تاريخ الإرسال</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $notification->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">تاريخ القراءة</span>
                        <span class="text-sm text-gray-900 dark:text-white">
                            {{ $notification->read_at ? $notification->read_at->format('Y-m-d H:i') : 'لم يُقرأ بعد' }}
                        </span>
                    </div>

                    @if($notification->expires_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">ينتهي في</span>
                            <span class="text-sm {{ $notification->isExpired() ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                                {{ $notification->expires_at->format('Y-m-d H:i') }}
                            </span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">الحالة</span>
                        <span class="text-sm font-medium {{ $notification->is_read ? 'text-green-600' : 'text-blue-600' }}">
                            {{ $notification->is_read ? 'مقروء' : 'جديد' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- إجراءات -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">إجراءات</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if(!$notification->is_read)
                        <button onclick="markAsRead()" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-check ml-2"></i>
                            تحديد كمقروء
                        </button>
                    @endif
                    
                    <button onclick="deleteNotification()" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-trash ml-2"></i>
                        حذف الإشعار
                    </button>
                    
                    <a href="{{ route('notifications') }}" 
                       class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                        <i class="fas fa-list ml-2"></i>
                        جميع الإشعارات
                    </a>
                </div>
            </div>

            <!-- إشعارات أخرى -->
            @php
                $otherNotifications = auth()->user()->notifications()
                                                 ->where('id', '!=', $notification->id)
                                                 ->valid()
                                                 ->orderBy('created_at', 'desc')
                                                 ->take(5)
                                                 ->get();
            @endphp

            @if($otherNotifications->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">إشعارات أخرى</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($otherNotifications as $other)
                                <a href="{{ route('notifications.show', $other) }}" 
                                   class="block p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                                            @if($other->type_color == 'blue') bg-blue-100 dark:bg-blue-900
                                            @elseif($other->type_color == 'green') bg-green-100 dark:bg-green-900
                                            @elseif($other->type_color == 'yellow') bg-yellow-100 dark:bg-yellow-900
                                            @elseif($other->type_color == 'red') bg-red-100 dark:bg-red-900
                                            @elseif($other->type_color == 'purple') bg-purple-100 dark:bg-purple-900
                                            @elseif($other->type_color == 'orange') bg-orange-100 dark:bg-orange-900
                                            @else bg-gray-100 dark:bg-gray-900
                                            @endif">
                                            <i class="{{ $other->type_icon }} text-sm
                                                @if($other->type_color == 'blue') text-blue-600 dark:text-blue-400
                                                @elseif($other->type_color == 'green') text-green-600 dark:text-green-400
                                                @elseif($other->type_color == 'yellow') text-yellow-600 dark:text-yellow-400
                                                @elseif($other->type_color == 'red') text-red-600 dark:text-red-400
                                                @elseif($other->type_color == 'purple') text-purple-600 dark:text-purple-400
                                                @elseif($other->type_color == 'orange') text-orange-600 dark:text-orange-400
                                                @else text-gray-600 dark:text-gray-400
                                                @endif"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $other->title }}</p>
                                                @if(!$other->is_read)
                                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $other->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function markAsRead() {
    fetch(`{{ route('notifications.mark-read', $notification) }}`, {
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

function deleteNotification() {
    if (confirm('هل تريد حذف هذا الإشعار؟')) {
        fetch(`{{ route('notifications.destroy', $notification) }}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("notifications") }}';
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

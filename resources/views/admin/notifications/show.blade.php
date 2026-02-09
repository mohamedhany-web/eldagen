@extends('layouts.app')

@section('title', 'تفاصيل الإشعار')
@section('header', 'تفاصيل الإشعار: ' . $notification->title)

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.notifications.index') }}" class="hover:text-primary-600">الإشعارات</a>
                <span class="mx-2">/</span>
                <span>{{ $notification->title }}</span>
            </nav>
        </div>
        <a href="{{ route('admin.notifications.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة
        </a>
    </div>

    <!-- تفاصيل الإشعار -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- المحتوى الرئيسي -->
        <div class="xl:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">تفاصيل الإشعار</h3>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $notification->is_read ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                            {{ $notification->is_read ? 'مقروء' : 'غير مقروء' }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <!-- العنوان والنوع -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">العنوان</label>
                            <div class="font-semibold text-xl text-gray-900 dark:text-white">{{ $notification->title }}</div>
                        </div>

                        <!-- النص -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">النص</label>
                            <div class="text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-4 rounded-lg whitespace-pre-wrap">{{ $notification->message }}</div>
                        </div>

                        <!-- معلومات التصنيف -->
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">نوع الإشعار</label>
                                <div class="text-gray-900 dark:text-white">{{ \App\Models\Notification::getTypes()[$notification->type] ?? $notification->type }}</div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الأولوية</label>
                                <div class="text-gray-900 dark:text-white">{{ \App\Models\Notification::getPriorities()[$notification->priority] ?? $notification->priority }}</div>
                            </div>
                        </div>

                        <!-- معلومات الاستهداف -->
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">نوع الاستهداف</label>
                                <div class="text-gray-900 dark:text-white">{{ \App\Models\Notification::getTargetTypes()[$notification->target_type] ?? $notification->target_type }}</div>
                            </div>
                            
                            @if($notification->target_id)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الهدف المحدد</label>
                                    <div class="text-gray-900 dark:text-white">ID: {{ $notification->target_id }}</div>
                                </div>
                            @endif
                        </div>

                        <!-- رابط الإجراء -->
                        @if($notification->action_url && $notification->action_text)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">رابط الإجراء</label>
                                <div class="flex items-center gap-3">
                                    <a href="{{ $notification->action_url }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                        {{ $notification->action_text }}
                                        <i class="fas fa-external-link-alt mr-2"></i>
                                    </a>
                                    <code class="text-sm bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $notification->action_url }}</code>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- معلومات المستقبل -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">المستقبل</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                            <span class="text-primary-600 dark:text-primary-400 font-medium">
                                {{ substr($notification->user->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $notification->user->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $notification->user->email }}</div>
                            @if($notification->user->phone)
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $notification->user->phone }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">إحصائيات</h3>
                </div>
                <div class="p-6 space-y-3">
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

                    @if($notification->read_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">وقت الاستجابة</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $notification->read_at->diffInMinutes($notification->created_at) }} دقيقة</span>
                        </div>
                    @endif

                    @if($notification->expires_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">ينتهي في</span>
                            <span class="text-sm {{ $notification->isExpired() ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                                {{ $notification->expires_at->format('Y-m-d H:i') }}
                            </span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">ID الإشعار</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $notification->id }}</span>
                    </div>
                </div>
            </div>

            <!-- إجراءات الأدمن -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">إجراءات</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.notifications.create') }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                        <i class="fas fa-plus ml-2"></i>
                        إرسال إشعار جديد
                    </a>
                    
                    <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-trash ml-2"></i>
                            حذف الإشعار
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

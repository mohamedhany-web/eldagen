@extends('layouts.app')

@section('title', 'تفاصيل النشاط - منصة الطارق في الرياضيات')
@section('header', 'تفاصيل النشاط')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تفاصيل النشاط</h1>
        <a href="{{ route('admin.activity-log') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-right"></i>
            العودة للقائمة
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-info-circle text-gray-500 dark:text-gray-400"></i>
                بيانات النشاط
            </h3>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 pb-2 border-b border-gray-200 dark:border-gray-600">معلومات أساسية</h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">نوع النشاط</label>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium mt-1 bg-slate-100 text-slate-700 dark:bg-slate-700/50 dark:text-slate-300">
                            @switch($activityLog->type)
                                @case('create') إنشاء @break
                                @case('update') تحديث @break
                                @case('delete') حذف @break
                                @case('login') تسجيل دخول @break
                                @case('logout') تسجيل خروج @break
                                @default {{ $activityLog->type }}
                            @endswitch
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">الوصف</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $activityLog->description }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">التاريخ والوقت</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $activityLog->created_at->format('Y-m-d H:i') }} <span class="text-gray-500 dark:text-gray-400">({{ $activityLog->created_at->diffForHumans() }})</span></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 pb-2 border-b border-gray-200 dark:border-gray-600">المستخدم</h4>
                    @if($activityLog->user)
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-sm font-medium flex-shrink-0 bg-indigo-500 dark:bg-indigo-600">
                                {{ substr($activityLog->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activityLog->user->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $activityLog->user->email ?? '—' }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">#{{ $activityLog->user->id }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">مجهول</p>
                    @endif
                </div>
            </div>

            @if($activityLog->model_type && $activityLog->model_id)
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">النموذج المرتبط</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">نوع النموذج</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ class_basename($activityLog->model_type) }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">المعرف</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $activityLog->model_id }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if($activityLog->data && is_array($activityLog->data) && count($activityLog->data) > 0)
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">البيانات الإضافية</h4>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                    <pre class="text-xs text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ json_encode($activityLog->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif

            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">معلومات تقنية</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">عنوان IP</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $activityLog->ip_address ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">وكيل المستخدم</label>
                        <p class="mt-1 text-gray-900 dark:text-white break-all text-xs">{{ Str::limit($activityLog->user_agent ?? '—', 80) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

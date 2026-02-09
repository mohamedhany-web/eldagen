@extends('layouts.app')

@section('title', 'سجل النشاطات - منصة الطارق في الرياضيات')
@section('header', 'سجل النشاطات')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">سجل النشاطات</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">مراقبة وتتبع العمليات في المنصة</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-search text-gray-500 dark:text-gray-400"></i>
                بحث وتصفية
            </h3>
        </div>
        <form method="GET" action="{{ route('admin.activity-log') }}" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نوع النشاط</label>
                    <select name="type" id="type" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
                        <option value="">جميع الأنواع</option>
                        <option value="create" {{ request('type') == 'create' ? 'selected' : '' }}>إنشاء</option>
                        <option value="update" {{ request('type') == 'update' ? 'selected' : '' }}>تحديث</option>
                        <option value="delete" {{ request('type') == 'delete' ? 'selected' : '' }}>حذف</option>
                        <option value="login" {{ request('type') == 'login' ? 'selected' : '' }}>تسجيل دخول</option>
                        <option value="logout" {{ request('type') == 'logout' ? 'selected' : '' }}>تسجيل خروج</option>
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">من تاريخ</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">إلى تاريخ</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-2.5 rounded-xl font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-search ml-2"></i>
                        بحث
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-history text-gray-500 dark:text-gray-400"></i>
                النشاطات ({{ $activities->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">المستخدم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">الوصف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">الوقت</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($activities as $activity)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                @if($activity->user)
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm font-medium flex-shrink-0 bg-indigo-500 dark:bg-indigo-600">
                                        {{ substr($activity->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity->user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->user->email ?: '—' }}</div>
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-300 dark:bg-gray-600">
                                        <i class="fas fa-user text-gray-500 dark:text-gray-400"></i>
                                    </div>
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">مجهول</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-700/50 dark:text-slate-300">
                                @switch($activity->type)
                                    @case('create') إنشاء @break
                                    @case('update') تحديث @break
                                    @case('delete') حذف @break
                                    @case('login') دخول @break
                                    @case('logout') خروج @break
                                    @default {{ $activity->type }}
                                @endswitch
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $activity->description }}</div>
                            @if($activity->model_type && $activity->model_id)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ class_basename($activity->model_type) }} #{{ $activity->model_id }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            <div>{{ $activity->created_at->format('Y-m-d') }}</div>
                            <div>{{ $activity->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.activity-log.show', $activity) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-eye"></i>
                                عرض
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="w-14 h-14 rounded-xl mx-auto mb-3 flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                                <i class="fas fa-history text-xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            لا توجد نشاطات
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $activities->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

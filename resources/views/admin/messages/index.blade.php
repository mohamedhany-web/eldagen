@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('إدارة الرسائل') }}</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ __('إرسال الرسائل والتقارير عبر الواتساب') }}</p>
            </div>
            <div class="flex space-x-2 space-x-reverse">
                <a href="{{ route('admin.messages.create') }}" 
                   class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    {{ __('رسالة جديدة') }}
                </a>
                <a href="{{ route('admin.messages.monthly-reports') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-chart-line ml-2"></i>
                    {{ __('التقارير الشهرية') }}
                </a>
                <a href="{{ route('admin.messages.templates') }}" 
                   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-file-alt ml-2"></i>
                    {{ __('قوالب الرسائل') }}
                </a>
                <a href="{{ route('admin.messages.settings') }}" 
                   class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-cog ml-2"></i>
                    {{ __('إعداد WhatsApp API') }}
                </a>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <i class="fas fa-envelope text-blue-600 dark:text-blue-300 text-xl"></i>
                </div>
                <div class="mr-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $stats['total_messages'] }}
                    </div>
                    <div class="text-gray-600 dark:text-gray-400 text-sm">
                        {{ __('إجمالي الرسائل') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <i class="fas fa-paper-plane text-green-600 dark:text-green-300 text-xl"></i>
                </div>
                <div class="mr-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $stats['sent_today'] }}
                    </div>
                    <div class="text-gray-600 dark:text-gray-400 text-sm">
                        {{ __('رسائل اليوم') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-300 text-xl"></i>
                </div>
                <div class="mr-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $stats['failed_messages'] }}
                    </div>
                    <div class="text-gray-600 dark:text-gray-400 text-sm">
                        {{ __('رسائل فاشلة') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                    <i class="fas fa-chart-bar text-purple-600 dark:text-purple-300 text-xl"></i>
                </div>
                <div class="mr-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $stats['monthly_reports'] }}
                    </div>
                    <div class="text-gray-600 dark:text-gray-400 text-sm">
                        {{ __('تقارير هذا الشهر') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- فلاتر البحث -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('البحث') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="{{ __('البحث في الرسائل...') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('الحالة') }}</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">{{ __('جميع الحالات') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('في الانتظار') }}</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>{{ __('تم الإرسال') }}</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>{{ __('تم التسليم') }}</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>{{ __('فشل الإرسال') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('النوع') }}</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">{{ __('جميع الأنواع') }}</option>
                    <option value="text" {{ request('type') === 'text' ? 'selected' : '' }}>{{ __('رسالة نصية') }}</option>
                    <option value="template" {{ request('type') === 'template' ? 'selected' : '' }}>{{ __('قالب') }}</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" 
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-search ml-2"></i>
                    {{ __('بحث') }}
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة الرسائل -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __('سجل الرسائل') }}
                <span class="text-sm text-gray-500">({{ $messages->total() }} رسالة)</span>
            </h3>
        </div>

        @if($messages->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('المستلم') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('الرسالة') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('الحالة') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('تاريخ الإرسال') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('الإجراءات') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($messages as $message)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                            <span class="text-primary-600 dark:text-primary-400 font-medium">
                                                {{ $message->user ? substr($message->user->name, 0, 1) : 'غ' }}
                                            </span>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $message->user->name ?? 'غير معروف' }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $message->phone_number }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white max-w-xs">
                                        {{ Str::limit($message->message, 100) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($message->status_color === 'green') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($message->status_color === 'red') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @elseif($message->status_color === 'blue') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($message->status_color === 'yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                        @endif">
                                        {{ $message->status_text }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $message->sent_at ? $message->sent_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-1 space-x-reverse">
                                        <a href="{{ route('admin.messages.show', $message) }}" 
                                           class="text-blue-600 hover:text-blue-800 p-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($message->status === 'failed')
                                            <form action="{{ route('admin.messages.resend', $message) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-800 p-1"
                                                        onclick="return confirm('هل تريد إعادة إرسال هذه الرسالة؟')">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 p-1"
                                                    onclick="return confirm('هل تريد حذف هذه الرسالة؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4">
                {{ $messages->withQueryString()->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('لا توجد رسائل') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('ابدأ بإرسال أول رسالة للطلاب') }}
                </p>
                <a href="{{ route('admin.messages.create') }}" 
                   class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                    <i class="fas fa-plus ml-2"></i>
                    {{ __('إرسال رسالة') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

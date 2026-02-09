@extends('layouts.app')

@section('title', 'إحصائيات المستخدمين - منصة الطارق في الرياضيات')
@section('header', 'إحصائيات المستخدمين')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إحصائيات المستخدمين</h1>
        <a href="{{ route('admin.statistics') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-right"></i>
            العودة للإحصائيات
        </a>
    </div>

    <!-- توزيع حسب الدور -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-users-cog text-gray-500 dark:text-gray-400"></i>
                توزيع المستخدمين حسب الدور
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $roleLabels = ['admin' => 'إداري', 'teacher' => 'مدرس', 'student' => 'طالب', 'parent' => 'ولي أمر'];
                @endphp
                @foreach($usersByRole as $row)
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $roleLabels[$row->role] ?? $row->role }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($row->count) }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- المستخدمون الجدد (آخر 12 شهر) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-calendar-alt text-gray-500 dark:text-gray-400"></i>
                تسجيل المستخدمين (آخر 12 شهر)
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">الشهر / السنة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">عدد المستخدمين</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($usersPerMonth as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::createFromDate($row->year, $row->month, 1)->translatedFormat('F Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ number_format($row->count) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">لا توجد بيانات</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'الإحصائيات - منصة الطارق في الرياضيات')
@section('header', 'الإحصائيات')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">لوحة الإحصائيات</h1>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.statistics.users') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-users"></i>
                إحصائيات المستخدمين
            </a>
            <a href="{{ route('admin.statistics.courses') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                <i class="fas fa-book"></i>
                إحصائيات الكورسات
            </a>
        </div>
    </div>

    <!-- ملخص سريع -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-chart-bar text-gray-500 dark:text-gray-400"></i>
                ملخص سريع
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-indigo-500 dark:bg-indigo-600 text-white">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">إجمالي المستخدمين</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalUsers) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">+{{ $newUsersThisMonth }} هذا الشهر</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-400 dark:bg-gray-600 text-white">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">الطلاب</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalStudents) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $totalUsers ? round(($totalStudents / $totalUsers) * 100, 1) : 0 }}%</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-400 dark:bg-gray-600 text-white">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">المعلمين</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalTeachers) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $totalUsers ? round(($totalTeachers / $totalUsers) * 100, 1) : 0 }}%</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-400 dark:bg-gray-600 text-white">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">الكورسات</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalCourses) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($totalEnrollments) }} تسجيل</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- تفاصيل إضافية -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">السنوات الدراسية</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($totalAcademicYears) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">المواد الدراسية</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($totalSubjects) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">تسجيلات هذا الشهر</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($newEnrollmentsThisMonth) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- أكثر الكورسات تسجيلاً -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-trophy text-gray-500 dark:text-gray-400"></i>
                    أكثر الكورسات تسجيلاً
                </h3>
            </div>
            <div class="p-6">
                @if($popularCourses->count() > 0)
                    <ul class="space-y-3">
                        @foreach($popularCourses as $course)
                        <li class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $course->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $course->academicYear->name ?? '—' }} - {{ $course->academicSubject->name ?? '—' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-700/50 dark:text-slate-300 flex-shrink-0 mr-2">
                                {{ $course->enrollments_count }} طالب
                            </span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">لا توجد كورسات مسجلة</p>
                @endif
            </div>
        </div>

        <!-- النشاطات الأخيرة -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-history text-gray-500 dark:text-gray-400"></i>
                    النشاطات الأخيرة
                </h3>
                <a href="{{ route('admin.activity-log') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">عرض الكل</a>
            </div>
            <div class="p-6">
                @if($recentActivities->count() > 0)
                    <ul class="space-y-3">
                        @foreach($recentActivities as $activity)
                        <li class="flex items-start gap-3 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                <i class="fas fa-circle text-[6px]"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <span class="font-medium">{{ $activity->user ? $activity->user->name : 'مجهول' }}</span>
                                    {{ $activity->description }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">لا توجد نشاطات</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

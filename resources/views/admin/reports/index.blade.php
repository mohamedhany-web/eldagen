@extends('layouts.app')

@section('title', 'التقارير - طلاب وبيانات')
@section('header', 'التقارير')

@section('content')
<div class="space-y-6">
    <p class="text-gray-600 dark:text-gray-400">تقارير شاملة عن الطلاب وكل بيانات كل طالب على المنصة.</p>

    <!-- إحصائيات عامة -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                    <i class="fas fa-user-graduate text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">إجمالي الطلاب</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalStudents) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">طلاب نشطون</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($activeStudents) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900 flex items-center justify-center">
                    <i class="fas fa-user-slash text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">موقوفون</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($suspendedStudents) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                    <i class="fas fa-book-reader text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">إجمالي التسجيلات</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalEnrollments) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center">
                    <i class="fas fa-play-circle text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">تسجيلات نشطة</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($activeEnrollments) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">محاولات امتحانات</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalExamAttempts) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900 flex items-center justify-center">
                    <i class="fas fa-video text-amber-600 dark:text-amber-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">تقدم دروس</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($totalLessonProgress) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- بحث وفلترة -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">قائمة الطلاب</h3>
        <form method="GET" class="flex flex-wrap gap-4 mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="الاسم، البريد، الجوال..."
                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white w-64">
            <select name="status" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                <option value="">كل الحالات</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>موقوف</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-search ml-2"></i> بحث
            </button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الاسم</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">البريد / الجوال</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">السنة</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">التسجيلات</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">محاولات الامتحانات</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">آخر دخول</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الحالة</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($students as $s)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $s->name }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            <div>{{ $s->email }}</div>
                            @if($s->phone)<div class="text-xs">{{ $s->phone }}</div>@endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            {{ $s->academicYear->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $s->course_enrollments_count }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $s->exam_attempts_count }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            {{ $s->last_login_at ? $s->last_login_at->diffForHumans() : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($s->suspended_at)
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">موقوف</span>
                            @elseif($s->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">نشط</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">غير نشط</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.messages.student', $s) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg">
                                    <i class="fas fa-chart-line ml-2"></i> عرض التقرير
                                </a>
                                <a href="{{ route('admin.messages.student.excel', $s) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg" title="تحميل Excel">
                                    <i class="fas fa-file-excel ml-2"></i> Excel
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">لا يوجد طلاب.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
        <div class="mt-4">
            {{ $students->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

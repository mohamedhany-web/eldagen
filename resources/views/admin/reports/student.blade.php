@extends('layouts.app')

@section('title', 'تقرير الطالب - ' . $user->name)
@section('header', 'تقرير الطالب')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <nav class="text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600">لوحة التحكم</a>
            <span class="mx-2">/</span>
            <a href="{{ route('admin.messages.index') }}" class="hover:text-indigo-600">التقارير</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700 dark:text-gray-300">{{ $user->name }}</span>
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.messages.student.excel', $user) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-file-excel ml-2"></i> تحميل Excel
            </a>
            <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-right ml-2"></i> العودة للتقارير
            </a>
        </div>
    </div>

    <!-- بيانات الطالب الأساسية -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">البيانات الأساسية</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">الاسم</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">البريد</p>
                <p class="text-gray-900 dark:text-white">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">الجوال</p>
                <p class="text-gray-900 dark:text-white">{{ $user->phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">جوال ولي الأمر</p>
                <p class="text-gray-900 dark:text-white">{{ $user->parent_phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">السنة الدراسية</p>
                <p class="text-gray-900 dark:text-white">{{ $user->academicYear->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">تاريخ الميلاد</p>
                <p class="text-gray-900 dark:text-white">{{ $user->birth_date ? $user->birth_date->format('Y-m-d') : '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">آخر دخول</p>
                <p class="text-gray-900 dark:text-white">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">الحالة</p>
                @if($user->suspended_at)
                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300">موقوف</span>
                @elseif($user->is_active)
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">نشط</span>
                @else
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">غير نشط</span>
                @endif
            </div>
        </div>
    </div>

    <!-- ملخص أرقام -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400">عدد التسجيلات</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $enrollments->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400">محاولات الامتحانات</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $examAttempts->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400">تقدم الدروس (سجلات)</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $lessonProgressCount }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400">دروس مكتملة</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $lessonCompletedCount }}</p>
        </div>
    </div>

    <!-- تسجيلات الكورسات -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">تسجيلات الكورسات</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الكورس</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">المادة / السنة</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الحالة</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">التقدم %</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">تاريخ التسجيل</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">تاريخ التفعيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($enrollments as $e)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $e->course->title ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            {{ $e->course->academicSubject->name ?? '—' }} / {{ $e->course->academicYear->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($e->status === 'active') bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300
                                @elseif($e->status === 'pending') bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300
                                @elseif($e->status === 'completed') bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300
                                @else bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                                @endif">{{ $e->status_text }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $e->progress ?? 0 }}%</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $e->enrolled_at ? $e->enrolled_at->format('Y-m-d') : '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $e->activated_at ? $e->activated_at->format('Y-m-d') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">لا يوجد تسجيلات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- محاولات الامتحانات -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">محاولات الامتحانات</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الامتحان</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الكورس</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الدرجة / النسبة</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الحالة</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">تاريخ البدء</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">تاريخ التسليم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($examAttempts as $a)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $a->exam->title ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $a->exam->course->title ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $a->score ?? '—' }} / {{ $a->percentage ? round($a->percentage, 1) . '%' : '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($a->status === 'submitted') bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300
                                @elseif($a->status === 'in_progress') bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300
                                @else bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                                @endif">{{ $a->status ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $a->started_at ? $a->started_at->format('Y-m-d H:i') : '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $a->submitted_at ? $a->submitted_at->format('Y-m-d H:i') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">لا توجد محاولات امتحانات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- الطلبات -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الطلبات</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الكورس</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">المبلغ</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">الحالة</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($orders as $o)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $o->id }}</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $o->course->title ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $o->amount ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($o->status === 'approved') bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300
                                @elseif($o->status === 'pending') bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300
                                @elseif($o->status === 'rejected') bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300
                                @else bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400
                                @endif">{{ $o->status ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $o->created_at ? $o->created_at->format('Y-m-d H:i') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">لا توجد طلبات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

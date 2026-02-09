@extends('layouts.welcome-layout')

@section('title', 'تقارير الأبناء - ولي الأمر')

@push('styles')
<style>
    .parent-report-tab { transition: all 0.2s ease; }
    .parent-report-tab.active { border-color: #6366f1; background: #eef2ff; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-10" x-data="{ activeStudent: 0 }">
    <!-- الهيدر -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-chart-pie text-indigo-600 ml-2"></i>
                تقارير الأبناء
            </h1>
            <p class="text-gray-500 mt-1">رقم الجوال: {{ $phone }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('parent-report.pdf') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors">
                <i class="fas fa-file-pdf ml-2"></i>
                تحميل التقرير PDF
            </a>
            <a href="{{ route('parent-report.exit') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-medium transition-colors">
                <i class="fas fa-sign-out-alt ml-2"></i>
                تغيير الرقم / خروج
            </a>
        </div>
    </div>

    <!-- تبويبات الأبناء -->
    @if(count($students) > 1)
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($students as $index => $s)
                <button type="button"
                        @click="activeStudent = {{ $index }}"
                        :class="activeStudent === {{ $index }} ? 'parent-report-tab active border-2' : 'parent-report-tab border border-gray-200 hover:border-indigo-300'"
                        class="px-4 py-2 rounded-xl font-medium text-gray-700">
                    {{ $s->name }}
                </button>
            @endforeach
        </div>
    @endif

    <!-- تقرير كل ابن -->
    @foreach($reports as $index => $r)
        <div x-show="activeStudent === {{ $index }}" x-transition class="space-y-6"
             :class="{ 'hidden': activeStudent !== {{ $index }} }">
            @php $user = $r['user']; @endphp

            <!-- بيانات أساسية -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-user text-indigo-600 ml-2"></i>
                        بيانات {{ $user->name }}
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">الاسم</p>
                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">السنة الدراسية</p>
                        <p class="text-gray-900">{{ $user->academicYear->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">آخر دخول</p>
                        <p class="text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">الحالة</p>
                        @if($user->suspended_at)
                            <span class="text-red-600 text-sm">موقوف</span>
                        @else
                            <span class="text-green-600 text-sm">نشط</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ملخص أرقام -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ $r['enrollments']->count() }}</p>
                    <p class="text-xs text-gray-500">تسجيلات كورسات</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $r['lessonCompletedCount'] }}</p>
                    <p class="text-xs text-gray-500">دروس مكتملة</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ $r['examAttempts']->count() }}</p>
                    <p class="text-xs text-gray-500">محاولات امتحانات</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-700">{{ $r['orders']->count() }}</p>
                    <p class="text-xs text-gray-500">طلبات</p>
                </div>
            </div>

            <!-- تسجيلات الكورسات -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">
                        <i class="fas fa-book-open text-indigo-600 ml-2"></i>
                        تسجيلات الكورسات
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-medium text-gray-700">الكورس</th>
                                <th class="px-4 py-3 font-medium text-gray-700">الحالة</th>
                                <th class="px-4 py-3 font-medium text-gray-700">التقدم %</th>
                                <th class="px-4 py-3 font-medium text-gray-700">تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($r['enrollments'] as $e)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $e->course->title ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 rounded-full text-xs
                                            @if($e->status === 'active') bg-green-100 text-green-800
                                            @elseif($e->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-700
                                            @endif">{{ $e->status_text }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $e->progress ?? 0 }}%</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $e->enrolled_at ? $e->enrolled_at->format('Y-m-d') : '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">لا يوجد تسجيلات.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- تقدم الدروس (آخر الدروس) -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">
                        <i class="fas fa-play-circle text-indigo-600 ml-2"></i>
                        تقدم الدروس (آخر {{ $r['lessonProgressDetails']->count() }} سجل)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-medium text-gray-700">الدرس</th>
                                <th class="px-4 py-3 font-medium text-gray-700">الكورس</th>
                                <th class="px-4 py-3 font-medium text-gray-700">مكتمل</th>
                                <th class="px-4 py-3 font-medium text-gray-700">وقت المشاهدة</th>
                                <th class="px-4 py-3 font-medium text-gray-700">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($r['lessonProgressDetails'] as $lp)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-900">{{ $lp->lesson->title ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $lp->lesson->course->title ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @if($lp->is_completed)
                                            <span class="text-green-600"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $lp->watch_time ? round($lp->watch_time / 60) . ' د' : '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $lp->completed_at ? $lp->completed_at->format('Y-m-d H:i') : ($lp->updated_at ? $lp->updated_at->format('Y-m-d') : '—') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">لا يوجد سجلات تقدم.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- محاولات الامتحانات -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">
                        <i class="fas fa-file-alt text-indigo-600 ml-2"></i>
                        محاولات الامتحانات
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-medium text-gray-700">الامتحان</th>
                                <th class="px-4 py-3 font-medium text-gray-700">الدرجة / النسبة</th>
                                <th class="px-4 py-3 font-medium text-gray-700">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($r['examAttempts'] as $a)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $a->exam->title ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ $a->score ?? '—' }} / {{ $a->percentage ? round($a->percentage, 1) . '%' : '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $a->submitted_at ? $a->submitted_at->format('Y-m-d H:i') : ($a->started_at ? $a->started_at->format('Y-m-d H:i') : '—') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">لا توجد محاولات.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- الطلبات -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">
                        <i class="fas fa-shopping-cart text-indigo-600 ml-2"></i>
                        الطلبات
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-medium text-gray-700">الكورس</th>
                                <th class="px-4 py-3 font-medium text-gray-700">المبلغ</th>
                                <th class="px-4 py-3 font-medium text-gray-700">الحالة</th>
                                <th class="px-4 py-3 font-medium text-gray-700">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($r['orders'] as $o)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-900">{{ $o->course->title ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ $o->amount ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 rounded-full text-xs
                                            @if($o->status === 'approved') bg-green-100 text-green-800
                                            @elseif($o->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-700
                                            @endif">{{ $o->status ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $o->created_at ? $o->created_at->format('Y-m-d H:i') : '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">لا توجد طلبات.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- آخر النشاطات على المنصة -->
            @if($r['recentActivity']->count() > 0)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">
                            <i class="fas fa-history text-indigo-600 ml-2"></i>
                            آخر النشاطات على المنصة
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-200 max-h-80 overflow-y-auto">
                        @foreach($r['recentActivity'] as $act)
                            <div class="px-6 py-3 text-sm">
                                <p class="text-gray-900">{{ $act->description ?? $act->action }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $act->created_at ? $act->created_at->format('Y-m-d H:i') : '' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection

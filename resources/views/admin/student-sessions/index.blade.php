@extends('layouts.app')

@section('title', 'تسجيلات دخول الطلاب - منصة الطارق في الرياضيات')
@section('header', 'التحكم بتسجيلات دخول الطلاب')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-green-800 dark:text-green-200">
            <i class="fas fa-check-circle ml-2"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-red-800 dark:text-red-200">
            <i class="fas fa-exclamation-circle ml-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-mobile-alt text-xl"></i>
                    تسجيلات دخول الطلاب (جهاز واحد لكل طالب)
                </h2>
                <form method="GET" action="{{ route('admin.student-sessions.index') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="بحث بالاسم أو الهاتف..."
                           class="px-4 py-2 rounded-xl border border-white/30 bg-white/10 text-white placeholder-white/70 focus:ring-2 focus:ring-white/50 focus:border-white/50 min-w-[180px]">
                    <button type="submit" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl font-medium transition-colors">
                        <i class="fas fa-search ml-2"></i>
                        بحث
                    </button>
                </form>
            </div>
        </div>

        <div class="p-4 text-sm text-gray-600 dark:text-gray-400 bg-blue-50/50 dark:bg-blue-900/10 border-b border-gray-200 dark:border-gray-700">
            <i class="fas fa-info-circle ml-2 text-blue-600 dark:text-blue-400"></i>
            الطالب لا يستطيع فتح حسابه إلا من جهاز واحد. إذا أراد الطالب الدخول من جهاز آخر، يتواصل مع الإدارة ثم تضغط هنا على «إلغاء تسجيل الدخول» ليتمكن من فتح الحساب على الجهاز الجديد.
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">الطالب</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">الهاتف</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">حالة الجهاز المسجّل</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">آخر نشاط</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    {{ substr($student->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $student->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400" dir="ltr">{{ $student->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($student->studentDevice)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                    <i class="fas fa-check-circle"></i>
                                    مسجّل من جهاز
                                </span>
                                @if($student->studentDevice->ip_address)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $student->studentDevice->ip_address }}</div>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                    <i class="fas fa-minus-circle"></i>
                                    لم يسجّل بعد
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            @if($student->studentDevice?->last_activity)
                                {{ $student->studentDevice->last_activity->diffForHumans() }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($student->studentDevice)
                                <form action="{{ route('admin.student-sessions.revoke', $student) }}" method="POST" class="inline" onsubmit="return confirm('هل تريد إلغاء تسجيل الدخول لهذا الطالب؟ سيُخرج من الجهاز الحالي ويمكنه الدخول من جهاز جديد.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium bg-amber-100 text-amber-800 hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:hover:bg-amber-900/50 transition-colors">
                                        <i class="fas fa-sign-out-alt"></i>
                                        إلغاء تسجيل الدخول
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-user-graduate text-4xl mb-3 block text-gray-300 dark:text-gray-600"></i>
                            لا يوجد طلاب أو لا توجد نتائج للبحث.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

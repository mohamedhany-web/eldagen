@extends('layouts.app')

@section('title', 'الطلاب المخالفون')
@section('header', 'الطلاب المخالفون — الحسابات الموقوفة')

@section('content')
<div class="space-y-6">
    <nav class="text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
        <span class="mx-2">/</span>
        <span>إدارة النظام</span>
        <span class="mx-2">/</span>
        <span>الطلاب المخالفون</span>
    </nav>

    @if(session('success'))
        <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                <i class="fas fa-user-slash text-red-500 ml-2"></i>
                الطلاب الذين تم تعليق حساباتهم بسبب مخالفة القواعد
            </h2>
        </div>
        <p class="px-6 py-2 text-sm text-gray-500 dark:text-gray-400">
            (سكرين شوت أو تسجيل شاشة أثناء <strong>مشاهدة فيديو الدرس</strong> أو أثناء <strong>الامتحان</strong> — يمكنك إعادة تفعيل الحساب من الزر أدناه)
        </p>

        @if($students->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-6 py-3 text-sm font-semibold">الطالب</th>
                            <th class="px-6 py-3 text-sm font-semibold">البريد / الجوال</th>
                            <th class="px-6 py-3 text-sm font-semibold">سبب التعليق</th>
                            <th class="px-6 py-3 text-sm font-semibold">مكان المخالفة</th>
                            <th class="px-6 py-3 text-sm font-semibold">تاريخ التعليق</th>
                            <th class="px-6 py-3 text-sm font-semibold">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($students as $user)
                            @php
                                $latestViolation = $user->accountViolations->first();
                                $contextNote = $latestViolation ? ($latestViolation->notes ?? '') : '';
                                $contextLabel = $contextNote === 'امتحان' ? 'امتحان' : ($contextNote === 'درس' ? 'درس / فيديو' : ($contextNote ?: '—'));
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $user->email ?? '—' }} @if($user->phone) / {{ $user->phone }} @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($user->suspension_reason === 'screenshot')
                                        <span class="px-2 py-1 rounded bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200">سكرين شوت / تصوير شاشة</span>
                                    @elseif($user->suspension_reason === 'recording')
                                        <span class="px-2 py-1 rounded bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200">تسجيل شاشة (سكرين ريكورد)</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $user->suspension_reason ?? 'مخالفة' }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($contextLabel === 'امتحان')
                                        <span class="px-2 py-1 rounded bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200">امتحان</span>
                                    @elseif($contextLabel === 'درس / فيديو')
                                        <span class="px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200">درس / فيديو</span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">{{ $contextLabel }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $user->suspended_at?->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.suspended-students.reinstate', $user) }}" method="POST" class="inline" onsubmit="return confirm('إعادة تفعيل حساب «{{ $user->name }}»؟');">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                                            <i class="fas fa-check-circle ml-1"></i>
                                            إعادة تفعيل الحساب
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $students->links() }}
            </div>
        @else
            <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
                <p class="text-lg font-medium">لا يوجد طلاب موقوفون حالياً</p>
                <p class="text-sm mt-1">ستظهر هنا الحسابات التي تم تعليقها بسبب مخالفة قواعد الاستخدام (سكرين شوت أو تسجيل).</p>
            </div>
        @endif
    </div>
</div>
@endsection

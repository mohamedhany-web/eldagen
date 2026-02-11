@extends('layouts.app')

@section('title', 'أكواد التفعيل')
@section('header', 'أكواد التفعيل')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- إحصائيات عامة -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">إجمالي الأكواد</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                </div>
                <i class="fas fa-ticket-alt text-blue-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">متاحة</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['active']) }}</p>
                </div>
                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">مستخدمة</p>
                    <p class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ number_format($stats['used']) }}</p>
                </div>
                <i class="fas fa-user-check text-amber-500 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- توليد أكواد جديدة -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">توليد أكواد جديدة</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.activation-codes.store') }}" method="POST" class="flex flex-wrap items-end gap-4">
                @csrf
                <div class="min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الكورس</label>
                    <select name="advanced_course_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                        <option value="">اختر الكورس</option>
                        @foreach($allCourses as $c)
                            <option value="{{ $c->id }}" {{ old('advanced_course_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                        @endforeach
                    </select>
                    @error('advanced_course_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">العدد</label>
                    <input type="number" name="quantity" value="{{ old('quantity', 5) }}" min="1" max="500" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    @error('quantity')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    توليد الأكواد
                </button>
            </form>
        </div>
    </div>

    <!-- قائمة الكورسات التي لها أكواد -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الكورسات وأكواد التفعيل</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">اختر كورساً للدخول إلى صفحته وعرض الأكواد أو تحميلها Excel</p>
        </div>
        <div class="p-6">
            @if($coursesWithCodes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($coursesWithCodes as $c)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-xl p-5 hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors bg-gray-50/50 dark:bg-gray-700/30">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-3 line-clamp-2">{{ $c->title }}</h4>
                            <div class="flex flex-wrap gap-3 text-sm mb-4">
                                <span class="text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-ticket-alt ml-1"></i>
                                    {{ number_format($c->activation_codes_count ?? 0) }} إجمالي
                                </span>
                                <span class="text-green-600 dark:text-green-400">
                                    <i class="fas fa-check-circle ml-1"></i>
                                    {{ number_format($c->active_codes_count ?? 0) }} متاحة
                                </span>
                                <span class="text-amber-600 dark:text-amber-400">
                                    <i class="fas fa-user-check ml-1"></i>
                                    {{ number_format($c->used_codes_count ?? 0) }} مستخدمة
                                </span>
                            </div>
                            <a href="{{ route('admin.activation-codes.show', $c) }}"
                               class="inline-flex items-center gap-2 w-full justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                                <i class="fas fa-list"></i>
                                عرض الأكواد / تحميل Excel
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-box-open text-4xl mb-4"></i>
                    <p>لا يوجد أي كورس له أكواد تفعيل حتى الآن.</p>
                    <p class="text-sm mt-2">قم بتوليد أكواد من النموذج أعلاه لأي كورس، ثم سيظهر هنا.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- تصدير الكل (اختياري) -->
    <div class="flex justify-end">
        <a href="{{ route('admin.activation-codes.export') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium transition-colors shadow-lg shadow-emerald-500/25">
            <i class="fas fa-file-excel"></i>
            تحميل كل الأكواد Excel
        </a>
    </div>
</div>
@endsection

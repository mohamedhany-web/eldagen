@extends('layouts.app')

@section('title', 'السنوات الدراسية')
@section('header', 'السنوات الدراسية')

@section('content')
<div class="space-y-6">
    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- إجمالي السنوات -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">إجمالي السنوات</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $academicYears->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-blue-600 dark:text-blue-400">السنوات الدراسية المتاحة</span>
            </div>
        </div>

        <!-- السنوات النشطة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">نشطة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $academicYears->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-green-600 dark:text-green-400">متاحة للتسجيل</span>
            </div>
        </div>

        <!-- المواد المرتبطة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">المواد المرتبطة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $academicYears->sum(function($year) { return $year->academicSubjects->count(); }) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-purple-600 dark:text-purple-400">مواد دراسية</span>
            </div>
        </div>

        <!-- الكورسات المتاحة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">الكورسات</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $academicYears->sum(function($year) { return $year->advancedCourses->count(); }) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-orange-600 dark:text-orange-400">كورسات متاحة</span>
            </div>
        </div>
    </div>

    <!-- إدارة السنوات الدراسية -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">السنوات الدراسية</h3>
                <a href="{{ route('admin.academic-years.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    إضافة سنة دراسية
                </a>
            </div>
        </div>
        <div class="p-6">
            @if($academicYears->count() > 0)
                <div class="space-y-4">
                    @foreach($academicYears as $year)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white" 
                                 style="background-color: {{ $year->color ?? '#3B82F6' }}">
                                <i class="{{ $year->icon ?? 'fas fa-calendar-alt' }} text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $year->name }}</h4>
                                @if($year->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $year->description }}</p>
                                @endif
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-book mr-1"></i>
                                        {{ $year->academicSubjects->count() }} مادة
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-graduation-cap mr-1"></i>
                                        {{ $year->advancedCourses->count() }} كورس
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $year->is_active 
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' 
                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                {{ $year->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.academic-years.edit', $year) }}" 
                                   class="p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-alt text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">لا توجد سنوات دراسية</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">ابدأ بإضافة السنوات الدراسية</p>
                    <a href="{{ route('admin.academic-years.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        إضافة سنة دراسية
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
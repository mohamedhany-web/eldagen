@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- الهيدر -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">السنوات الدراسية</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">اختر صفك الدراسي لاستكشاف المواد والكورسات</p>
                    </div>
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة لللوحة الرئيسية
                    </a>
                </div>
            </div>
        </div>

        <!-- السنوات الدراسية -->
        @if($academicYears->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($academicYears as $index => $year)
                <a href="{{ route('academic-years.subjects', $year) }}" 
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-br 
                        @if($index % 6 == 0) from-blue-400 to-blue-600
                        @elseif($index % 6 == 1) from-green-400 to-green-600
                        @elseif($index % 6 == 2) from-purple-400 to-purple-600
                        @elseif($index % 6 == 3) from-orange-400 to-orange-600
                        @elseif($index % 6 == 4) from-pink-400 to-pink-600
                        @else from-indigo-400 to-indigo-600
                        @endif
                        flex items-center justify-center relative overflow-hidden">
                        <i class="fas fa-
                            @if($index % 6 == 0) calculator
                            @elseif($index % 6 == 1) square-root-alt
                            @elseif($index % 6 == 2) infinity
                            @elseif($index % 6 == 3) chart-line
                            @elseif($index % 6 == 4) functions
                            @else pi
                            @endif
                            text-white text-6xl"></i>
                        @if($year->academic_subjects_count > 0)
                            <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1">
                                <span class="text-white text-sm font-medium">{{ $year->academic_subjects_count }} مادة</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $year->name }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            @if($year->description)
                                {{ Str::limit($year->description, 80) }}
                            @else
                                استكشف المواد والكورسات المتاحة لهذه السنة الدراسية
                            @endif
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-book text-gray-400"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $year->academic_subjects_count }} مادة متاحة</span>
                            </div>
                            <div class="flex items-center gap-1 text-blue-600 dark:text-blue-400">
                                <span class="text-sm font-medium">استكشف</span>
                                <i class="fas fa-arrow-left"></i>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8 text-center">
                <div class="text-gray-500 dark:text-gray-400">
                    <i class="fas fa-graduation-cap text-4xl mb-4"></i>
                    <p class="text-lg font-medium">لا توجد سنوات دراسية متاحة</p>
                    <p class="text-sm mt-2">لم يتم إضافة أي سنوات دراسية بعد</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection










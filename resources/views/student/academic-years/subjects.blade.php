@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- الهيدر -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $academicYear->name }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">المواد الدراسية المتاحة</p>
                    </div>
                    <a href="{{ route('academic-years') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة للسنوات الدراسية
                    </a>
                </div>
            </div>
        </div>

        <!-- المواد الدراسية -->
        @if($subjects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($subjects as $index => $subject)
                <a href="{{ route('subjects.courses', $subject) }}" 
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="h-40 bg-gradient-to-br 
                        @if($index % 5 == 0) from-emerald-400 to-emerald-600
                        @elseif($index % 5 == 1) from-cyan-400 to-cyan-600
                        @elseif($index % 5 == 2) from-violet-400 to-violet-600
                        @elseif($index % 5 == 3) from-rose-400 to-rose-600
                        @else from-amber-400 to-amber-600
                        @endif
                        flex items-center justify-center relative overflow-hidden">
                        <i class="fas fa-
                            @if($index % 5 == 0) flask
                            @elseif($index % 5 == 1) atom
                            @elseif($index % 5 == 2) dna
                            @elseif($index % 5 == 3) globe
                            @else book-open
                            @endif
                            text-white text-5xl"></i>
                        @if($subject->advanced_courses_count > 0)
                            <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1">
                                <span class="text-white text-sm font-medium">{{ $subject->advanced_courses_count }} كورس</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $subject->name }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                            @if($subject->description)
                                {{ Str::limit($subject->description, 60) }}
                            @else
                                استكشف الكورسات المتاحة لهذه المادة
                            @endif
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-graduation-cap text-gray-400"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $subject->advanced_courses_count }} كورس</span>
                            </div>
                            <div class="flex items-center gap-1 text-blue-600 dark:text-blue-400">
                                <span class="text-sm font-medium">عرض الكورسات</span>
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
                    <i class="fas fa-book text-4xl mb-4"></i>
                    <p class="text-lg font-medium">لا توجد مواد متاحة</p>
                    <p class="text-sm mt-2">لم يتم إضافة أي مواد دراسية لهذه السنة بعد</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection










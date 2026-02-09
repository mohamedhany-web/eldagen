@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- الهيدر -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $academicSubject->name }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $academicSubject->academicYear->name }} - الكورسات المتاحة</p>
                    </div>
                    <a href="{{ route('academic-years.subjects', $academicSubject->academicYear) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة للمواد
                    </a>
                </div>
            </div>
        </div>

        <!-- الكورسات -->
        @if($courses->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($courses as $course)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <!-- صورة الكورس -->
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center relative">
                        @if($course->image)
                            <img src="{{ storage_url($course->image) }}" alt="{{ $course->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-play-circle text-white text-6xl"></i>
                        @endif
                        
                        <!-- شارة المستوى -->
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($course->level == 'beginner') bg-green-100 text-green-800
                                @elseif($course->level == 'intermediate') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $course->level_badge['text'] ?? 'مبتدئ' }}
                            </span>
                        </div>

                        <!-- السعر -->
                        @if($course->price && $course->price > 0)
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-lg px-3 py-1">
                                <span class="text-gray-900 font-bold text-sm">{{ number_format($course->price) }} ج.م</span>
                            </div>
                        @else
                            <div class="absolute top-4 right-4 bg-green-500/90 backdrop-blur-sm rounded-lg px-3 py-1">
                                <span class="text-white font-bold text-sm">مجاني</span>
                            </div>
                        @endif
                    </div>

                    <!-- محتوى الكورس -->
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $course->title }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                            {{ Str::limit($course->description, 100) }}
                        </p>

                        <!-- معلومات الكورس -->
                        <div class="flex items-center gap-4 mb-4 text-xs text-gray-500 dark:text-gray-400">
                            @if($course->duration)
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $course->duration }}</span>
                                </div>
                            @endif
                            @if($course->lessons_count)
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-video"></i>
                                    <span>{{ $course->lessons_count }} درس</span>
                                </div>
                            @endif
                        </div>

                        <!-- أزرار العمل -->
                        <div class="flex gap-2">
                            <a href="{{ route('courses.show', $course) }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-colors">
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8 text-center">
                <div class="text-gray-500 dark:text-gray-400">
                    <i class="fas fa-graduation-cap text-4xl mb-4"></i>
                    <p class="text-lg font-medium">لا توجد كورسات متاحة</p>
                    <p class="text-sm mt-2">لم يتم إضافة أي كورسات لهذه المادة بعد</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection



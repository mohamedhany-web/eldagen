@extends('layouts.app')

@section('title', 'كورساتي المفعلة')
@section('header', 'كورساتي المفعلة')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">الكورسات التي تم تفعيلها لك من قبل الإدارة</p>
                </div>
                <a href="{{ route('catalog.index') }}" 
                   class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-search ml-2"></i>
                    كورسات
                </a>
            </div>
        </div>
    </div>

    <!-- الإحصائيات السريعة -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary-100 dark:bg-primary-900">
                    <i class="fas fa-book-open text-primary-600 dark:text-primary-400"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">الكورسات المفعلة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_active'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">مكتملة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_completed'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <i class="fas fa-clock text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">إجمالي الساعات</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_hours'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <i class="fas fa-chart-line text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">متوسط التقدم</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['avg_progress'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- الكورسات -->
    @if($activeCourses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($activeCourses as $course)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow duration-300">
                <!-- صورة الكورس -->
                <div class="h-48 bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                    @if($course->thumbnail)
                        <img src="{{ storage_url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="text-center text-white">
                            <i class="fas fa-graduation-cap text-4xl mb-2"></i>
                            <p class="text-sm">{{ $course->academicSubject->name ?? 'كورس' }}</p>
                        </div>
                    @endif
                </div>

                    <!-- محتوى الكورس -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white line-clamp-2">{{ $course->title }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mr-2">
                                مفعل
                            </span>
                        </div>

                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-graduation-cap w-4 ml-2"></i>
                                <span>{{ $course->academicYear->name ?? 'غير محدد' }} - {{ $course->academicSubject->name ?? 'غير محدد' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-user-tie w-4 ml-2"></i>
                                <span>{{ $course->teacher->name ?? 'غير محدد' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-play-circle w-4 ml-2"></i>
                                <span>{{ $course->lessons->count() }} درس</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock w-4 ml-2"></i>
                                <span>{{ $course->duration_hours }} ساعة</span>
                            </div>
                        </div>

                        <!-- شريط التقدم -->
                        @php
                            $progress = $course->pivot->progress ?? 0;
                        @endphp
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span class="text-gray-600 dark:text-gray-400">التقدم</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <!-- أزرار العمل -->
                        <div class="flex gap-2">
                            <a href="{{ route('my-courses.show', $course) }}" 
                               class="flex-1 bg-primary-600 hover:bg-primary-700 text-white py-2 px-4 rounded-lg text-center font-medium transition-colors">
                                <i class="fas fa-play ml-2"></i>
                                متابعة التعلم
                            </a>
                            @if($progress > 0)
                                <button class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-bookmark"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- الصفحات -->
            <div class="mt-8">
                {{ $activeCourses->links() }}
            </div>
    @else
        <!-- لا توجد كورسات -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-graduation-cap text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد كورسات مفعلة</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">لم يتم تفعيل أي كورسات لك بعد. يمكنك كورسات المتاحة وطلب تفعيلها.</p>
            <a href="{{ route('catalog.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-search ml-2"></i>
                كورسات
            </a>
        </div>
    @endif
</div>
@endsection

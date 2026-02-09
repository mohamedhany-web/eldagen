@extends('layouts.app')

@section('title', 'امتحاناتي')
@section('header', 'امتحاناتي')

@section('content')
<div class="w-full max-w-full min-w-0 space-y-4 sm:space-y-6 -mx-4 px-3 sm:mx-0 sm:px-0">
    <!-- الهيدر -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl sm:rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-4 sm:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">الامتحانات المتاحة من الكورسات المفعلة لك</p>
                <a href="{{ route('my-courses.index') }}" 
                   class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-medium transition-colors w-full sm:w-auto">
                    <i class="fas fa-book-open"></i>
                    كورساتي
                </a>
            </div>
        </div>
    </div>

    <!-- الامتحانات المتاحة -->
    @if($availableExams->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            @foreach($availableExams as $exam)
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- هيدر البطاقة -->
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white line-clamp-2">{{ $exam->title }}</h3>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($exam->can_attempt)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="fas fa-check-circle ml-1"></i>
                                    متاح
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <i class="fas fa-times-circle ml-1"></i>
                                    غير متاح
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- محتوى البطاقة -->
                <div class="p-4 sm:p-6">
                    <!-- معلومات الامتحان -->
                    <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                        <div class="flex items-start sm:items-center gap-2 text-sm">
                            <i class="fas fa-graduation-cap text-indigo-500 w-4 flex-shrink-0 mt-0.5 sm:mt-0"></i>
                            <span class="text-gray-600 dark:text-gray-400 flex-shrink-0">الكورس:</span>
                            <span class="text-gray-900 dark:text-white break-words">{{ $exam->course->title }}</span>
                        </div>
                        
                        <div class="flex items-start sm:items-center gap-2 text-sm">
                            <i class="fas fa-book text-indigo-500 w-4 flex-shrink-0 mt-0.5 sm:mt-0"></i>
                            <span class="text-gray-600 dark:text-gray-400 flex-shrink-0">المادة:</span>
                            <span class="text-gray-900 dark:text-white break-words">{{ $exam->course->academicSubject->name ?? 'غير محدد' }}</span>
                        </div>

                        @if($exam->lesson)
                            <div class="flex items-start sm:items-center gap-2 text-sm">
                                <i class="fas fa-play-circle text-indigo-500 w-4 flex-shrink-0 mt-0.5 sm:mt-0"></i>
                                <span class="text-gray-600 dark:text-gray-400 flex-shrink-0">الدرس:</span>
                                <span class="text-gray-900 dark:text-white break-words">{{ $exam->lesson->title }}</span>
                            </div>
                        @endif

                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-clock text-indigo-500 w-4 flex-shrink-0"></i>
                            <span class="text-gray-600 dark:text-gray-400">المدة:</span>
                            <span class="text-gray-900 dark:text-white">{{ $exam->duration_minutes }} دقيقة</span>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-question-circle text-indigo-500 w-4 flex-shrink-0"></i>
                            <span class="text-gray-600 dark:text-gray-400">عدد الأسئلة:</span>
                            <span class="text-gray-900 dark:text-white">{{ $exam->questions_count }} سؤال</span>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-star text-indigo-500 w-4 flex-shrink-0"></i>
                            <span class="text-gray-600 dark:text-gray-400">درجة النجاح:</span>
                            <span class="text-gray-900 dark:text-white">{{ $exam->passing_marks }}%</span>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <i class="fas fa-redo text-indigo-500 w-4 flex-shrink-0"></i>
                            <span class="text-gray-600 dark:text-gray-400">المحاولات:</span>
                            <span class="text-gray-900 dark:text-white">{{ $exam->attempts_allowed == 0 ? 'غير محدود' : $exam->attempts_allowed }}</span>
                        </div>
                    </div>

                    <!-- معلومات المحاولات -->
                    @if($exam->user_attempts > 0)
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-blue-900 dark:text-blue-100">محاولاتك السابقة</div>
                                    <div class="text-sm text-blue-700 dark:text-blue-300">
                                        {{ $exam->user_attempts }} من {{ $exam->attempts_allowed == 0 ? 'غير محدود' : $exam->attempts_allowed }}
                                    </div>
                                </div>
                                @if($exam->best_score !== null)
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ number_format($exam->best_score, 1) }}%</div>
                                        <div class="text-xs text-blue-700 dark:text-blue-300">أفضل نتيجة</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($exam->description)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $exam->description }}</p>
                        </div>
                    @endif

                    <!-- أزرار العمل -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 space-y-0.5">
                            @if($exam->start_time)
                                <div>يبدأ: {{ $exam->start_time->format('Y-m-d H:i') }}</div>
                            @endif
                            @if($exam->end_time)
                                <div>ينتهي: {{ $exam->end_time->format('Y-m-d H:i') }}</div>
                            @endif
                        </div>

                        <div class="flex-shrink-0">
                            @if($exam->can_attempt)
                                <a href="{{ route('student.exams.show', $exam) }}" 
                                   class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors w-full sm:w-auto">
                                    <i class="fas fa-play"></i>
                                    ابدأ الامتحان
                                </a>
                            @elseif($exam->user_attempts >= $exam->attempts_allowed && $exam->attempts_allowed > 0)
                                <span class="inline-block px-4 py-2 rounded-xl text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    استنفدت المحاولات
                                </span>
                            @else
                                <span class="inline-block px-4 py-2 rounded-xl text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    غير متاح حالياً
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- معلومات الأمان -->
                    @if($exam->prevent_tab_switch || $exam->require_camera || $exam->require_microphone)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2 text-xs text-yellow-700 dark:text-yellow-300">
                                <i class="fas fa-shield-alt"></i>
                                <span>امتحان محمي:</span>
                                @if($exam->prevent_tab_switch)
                                    <span class="bg-yellow-100 dark:bg-yellow-900 px-2 py-1 rounded">منع تبديل التبويبات</span>
                                @endif
                                @if($exam->require_camera)
                                    <span class="bg-yellow-100 dark:bg-yellow-900 px-2 py-1 rounded">يتطلب كاميرا</span>
                                @endif
                                @if($exam->require_microphone)
                                    <span class="bg-yellow-100 dark:bg-yellow-900 px-2 py-1 rounded">يتطلب مايكروفون</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- الامتحانات المكتملة -->
        @php
            $completedExams = $availableExams->filter(function($exam) {
                return $exam->last_attempt && in_array($exam->last_attempt->status, ['submitted', 'auto_submitted']);
            });
        @endphp

        @if($completedExams->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">الامتحانات المكتملة</h3>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-3">
                        @foreach($completedExams as $exam)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium text-gray-900 dark:text-white truncate">{{ $exam->title }}</div>
                                    <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                        {{ $exam->course->title }} — {{ $exam->last_attempt->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="text-center flex-shrink-0">
                                    <div class="text-lg font-bold {{ $exam->last_attempt->result_color == 'green' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($exam->last_attempt->percentage, 1) }}%
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $exam->last_attempt->result_status }}</div>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($exam->show_results_immediately)
                                        <a href="{{ route('student.exams.result', [$exam, $exam->last_attempt]) }}" 
                                           class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                            <i class="fas fa-chart-line"></i>
                                            عرض النتيجة
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl sm:rounded-lg border border-gray-200 dark:border-gray-700 p-8 sm:p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clipboard-check text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد امتحانات متاحة</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">لا توجد امتحانات متاحة في الكورسات المفعلة لك حالياً</p>
            <a href="{{ route('my-courses.index') }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors w-full sm:w-auto">
                <i class="fas fa-book-open"></i>
                عرض كورساتي
            </a>
        </div>
    @endif

    <!-- إحصائيات سريعة -->
    @if($availableExams->count() > 0)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 sm:p-3 rounded-xl bg-indigo-100 dark:bg-indigo-900 flex-shrink-0">
                        <i class="fas fa-clipboard-check text-indigo-600 dark:text-indigo-400 text-lg sm:text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $availableExams->count() }}</p>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">امتحانات متاحة</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 sm:p-3 rounded-xl bg-green-100 dark:bg-green-900 flex-shrink-0">
                        <i class="fas fa-check text-green-600 dark:text-green-400 text-lg sm:text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $completedExams->count() }}</p>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">مكتملة</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 sm:p-3 rounded-xl bg-amber-100 dark:bg-amber-900 flex-shrink-0">
                        <i class="fas fa-clock text-amber-600 dark:text-amber-400 text-lg sm:text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $availableExams->where('can_attempt', true)->count() }}</p>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">يمكن أداؤها</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-6 col-span-2 lg:col-span-1">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 sm:p-3 rounded-xl bg-purple-100 dark:bg-purple-900 flex-shrink-0">
                        <i class="fas fa-percentage text-purple-600 dark:text-purple-400 text-lg sm:text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        @php
                            $avgScore = $completedExams->where('best_score', '!=', null)->avg('best_score');
                        @endphp
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $avgScore ? number_format($avgScore, 1) : 0 }}%</p>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">متوسط النتائج</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

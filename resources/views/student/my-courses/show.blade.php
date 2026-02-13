@extends('layouts.focus')

@section('title', $course->title)

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col" x-data="{ sidebarOpen: true }">
    <!-- شريط علوي: العودة + عنوان الكورس + تقدم + زر السايدبار -->
    <div class="flex-shrink-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('my-courses.index') }}" id="back-to-courses-link" data-viewing-lesson="{{ $selectedLessonId && isset($canOpenLessonIds[$selectedLessonId]) ? '1' : '0' }}" class="flex-shrink-0 text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 p-1 rounded-lg transition-colors" title="العودة إلى كورساتي">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div class="min-w-0">
                <h1 class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ $course->title }}</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $completedLessons }} / {{ $totalLessons }} درس مكتمل — {{ $progress }}% @if($course->isStrictLessonAccess())<span class="text-indigo-500">(تسلسلي)</span>@else<span class="text-green-500">(حر)</span>@endif</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <div class="w-24 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden hidden sm:block">
                <div class="h-full bg-indigo-600 rounded-full transition-all" style="width: {{ $progress }}%"></div>
            </div>
            <button type="button" @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" :title="sidebarOpen ? 'إغلاق القائمة' : 'فتح القائمة'">
                <i class="fas" :class="sidebarOpen ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
            </button>
        </div>
    </div>

    <div class="flex-1 flex min-h-0">
        <!-- السايدبار: أقسام + دروس (قابل للإغلاق والفتح) -->
        <aside class="flex-shrink-0 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden transition-[width] duration-300 ease-out"
              :class="sidebarOpen ? 'w-72' : 'w-0 min-w-0 overflow-hidden border-0'">
            <div class="flex-1 overflow-y-auto py-4">
                @forelse($course->sections as $section)
                    <div class="mb-4">
                        <div class="px-4 py-2 text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-folder text-indigo-500"></i>
                            {{ $section->title }}
                        </div>
                        <ul class="space-y-0.5">
                            @foreach($section->lessons as $lesson)
                                @php
                                    $p = $lesson->progress->first();
                                    $durSec = max(1, (int) ($lesson->duration_minutes ?? 0) * 60);
                                    $watchPct = $p ? min(100, ($p->watch_time / $durSec) * 100) : 0;
                                    $isCompleted = $p && ($p->is_completed || $watchPct >= ($requiredPercent ?? 90));
                                    $canOpen = isset($canOpenLessonIds[$lesson->id]);
                                    $isSelected = $lesson->id == $selectedLessonId;
                                @endphp
                                <li>
                                    @if($canOpen)
                                        <a href="{{ route('my-courses.show', $course) }}?lesson={{ $lesson->id }}"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors {{ $isSelected ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                                            @if($isCompleted)
                                                <i class="fas fa-check-circle text-green-500 flex-shrink-0"></i>
                                            @else
                                                <i class="fas fa-play-circle text-indigo-500 flex-shrink-0"></i>
                                            @endif
                                            <span class="truncate">{{ $lesson->title }}</span>
                                            @if($lesson->duration_minutes)
                                                <span class="flex-shrink-0 text-xs text-gray-400">{{ $lesson->duration_minutes }} د</span>
                                            @endif
                                        </a>
                                    @else
                                        <div class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-400 dark:text-gray-500 cursor-not-allowed">
                                            <i class="fas fa-lock flex-shrink-0"></i>
                                            <span class="truncate">{{ $lesson->title }}</span>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                @endforelse

                @if($lessonsWithoutSection->isNotEmpty())
                    <div class="mb-4">
                        <div class="px-4 py-2 text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            دروس إضافية
                        </div>
                        <ul class="space-y-0.5">
                            @foreach($lessonsWithoutSection as $lesson)
                                @php
                                    $p = $lesson->progress->first();
                                    $durSec = max(1, (int) ($lesson->duration_minutes ?? 0) * 60);
                                    $watchPct = $p ? min(100, ($p->watch_time / $durSec) * 100) : 0;
                                    $isCompleted = $p && ($p->is_completed || $watchPct >= ($requiredPercent ?? 90));
                                    $canOpen = isset($canOpenLessonIds[$lesson->id]);
                                    $isSelected = $lesson->id == $selectedLessonId;
                                @endphp
                                <li>
                                    @if($canOpen)
                                        <a href="{{ route('my-courses.show', $course) }}?lesson={{ $lesson->id }}"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors {{ $isSelected ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                                            @if($isCompleted)
                                                <i class="fas fa-check-circle text-green-500 flex-shrink-0"></i>
                                            @else
                                                <i class="fas fa-play-circle text-indigo-500 flex-shrink-0"></i>
                                            @endif
                                            <span class="truncate">{{ $lesson->title }}</span>
                                        </a>
                                    @else
                                        <div class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-400 cursor-not-allowed">
                                            <i class="fas fa-lock flex-shrink-0"></i>
                                            <span class="truncate">{{ $lesson->title }}</span>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($course->sections->isEmpty() && $lessonsWithoutSection->isEmpty())
                    <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                        لا توجد أقسام أو دروس بعد.
                    </div>
                @endif
            </div>
        </aside>

        <!-- المحتوى الرئيسي: مشغل الفيديو (على الهاتف عرض ثابت لتحسين ظهور الفيديو) -->
        <main class="flex-1 min-w-0 flex flex-col bg-black overflow-x-auto">
            @if($selectedLessonId && $course->lessons->firstWhere('id', $selectedLessonId))
                @php
                    $selectedLesson = $course->lessons->firstWhere('id', $selectedLessonId);
                    $canOpenSelected = isset($canOpenLessonIds[$selectedLessonId]);
                @endphp
                @if($canOpenSelected && $selectedLesson->is_active)
                    <div class="flex-1 min-h-0 min-w-[800px] w-full flex flex-col">
                        <iframe id="lesson-viewer-iframe" src="{{ route('my-courses.lesson.watch', [$course, $selectedLesson]) }}"
                                class="w-full flex-1 min-h-0 border-0"
                                title="{{ $selectedLesson->title }}"
                                allow="encrypted-media; fullscreen"
                                allowfullscreen></iframe>
                    </div>
                @else
                    <div class="flex-1 flex items-center justify-center text-white p-8 text-center">
                        <div>
                            <i class="fas fa-lock text-4xl mb-4 opacity-70"></i>
                            <p class="text-lg font-medium">يجب إكمال الدروس السابقة أولاً</p>
                            <p class="text-sm text-gray-400 mt-2">اختر درساً من القائمة أو أكمل الدرس الحالي.</p>
                        </div>
                    </div>
                @endif
            @else
                <div class="flex-1 flex items-center justify-center text-white p-8 text-center">
                    <div>
                        <i class="fas fa-play-circle text-5xl mb-4 opacity-70"></i>
                        <p class="text-xl font-medium">اختر درساً من القائمة</p>
                        <p class="text-gray-400 mt-2">ستظهر المحاضرة هنا بمشغل فيديو محمي.</p>
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var backLink = document.getElementById('back-to-courses-link');
    if (!backLink) return;
    backLink.addEventListener('click', function(e) {
        if (this.getAttribute('data-viewing-lesson') !== '1') return;
        e.preventDefault();
        var iframe = document.getElementById('lesson-viewer-iframe');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage('showExitModal', '*');
        } else {
            window.location.href = this.getAttribute('href');
        }
    });
});
</script>
@endpush
@endsection

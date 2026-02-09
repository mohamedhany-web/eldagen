@extends('layouts.welcome-layout')

@section('title', 'كورسات')

@push('styles')
<style>
    .catalog-page-bg { background: linear-gradient(180deg, #eef2ff 0%, #e0e7ff 30%, #ffffff 100%); min-height: 100vh; }
    .floating-shape-catalog-index {
        position: absolute;
        opacity: 0.12;
        animation: floatCatalogIndex 8s ease-in-out infinite;
        pointer-events: none;
    }
    .floating-shape-catalog-index:nth-child(1) { top: 15%; right: 5%; width: 56px; height: 56px; border-radius: 50%; background: #818cf8; animation-delay: 0s; }
    .floating-shape-catalog-index:nth-child(2) { bottom: 30%; left: 8%; width: 40px; height: 40px; border-radius: 10px; background: #a5b4fc; animation-delay: 2s; }
    .floating-shape-catalog-index:nth-child(3) { top: 50%; right: 15%; width: 32px; height: 32px; border-radius: 50%; background: #667eea; animation-delay: 4s; }
    .floating-shape-catalog-index:nth-child(4) { bottom: 20%; right: 10%; width: 60px; height: 4px; background: linear-gradient(90deg, transparent, #764ba2, transparent); animation-delay: 1s; }
    .floating-shape-catalog-index:nth-child(5) { top: 25%; left: 20%; width: 48px; height: 48px; border-radius: 12px; background: #c4b5fd; animation-delay: 3s; }
    @keyframes floatCatalogIndex {
        0%, 100% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.12; }
        50% { transform: translateY(-20px) rotate(180deg) scale(1.05); opacity: 0.2; }
    }
    .floating-numbers-catalog-index {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        pointer-events: none;
        overflow: hidden;
    }
    .floating-number-catalog-index {
        position: absolute;
        color: rgba(102, 126, 234, 0.07);
        font-size: 1.25rem;
        font-weight: bold;
        animation: floatNumCatalog 16s linear infinite;
    }
    @keyframes floatNumCatalog {
        0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
        20% { opacity: 0.5; }
        80% { opacity: 0.2; }
        100% { transform: translateY(-20vh) rotate(360deg); opacity: 0; }
    }
    .card-catalog-index {
        transition: all 0.3s ease;
        box-shadow: 0 4px 18px rgba(0,0,0,0.06);
        border: 1px solid rgba(226, 232, 240, 0.9);
    }
    .card-catalog-index:hover {
        box-shadow: 0 12px 28px rgba(102, 126, 234, 0.12);
        transform: translateY(-3px);
    }
</style>
@endpush

@section('content')
<div class="relative catalog-page-bg pt-24 pb-10 overflow-hidden">
    <div class="floating-numbers-catalog-index">
        <span class="floating-number-catalog-index" style="left: 10%; animation-delay: 0s;">π</span>
        <span class="floating-number-catalog-index" style="left: 25%; animation-delay: 2s;">∞</span>
        <span class="floating-number-catalog-index" style="left: 45%; animation-delay: 4s;">∑</span>
        <span class="floating-number-catalog-index" style="left: 65%; animation-delay: 6s;">∫</span>
        <span class="floating-number-catalog-index" style="left: 85%; animation-delay: 8s;">θ</span>
    </div>
    <div class="floating-shape-catalog-index"></div>
    <div class="floating-shape-catalog-index"></div>
    <div class="floating-shape-catalog-index"></div>
    <div class="floating-shape-catalog-index"></div>
    <div class="floating-shape-catalog-index"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-10">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- السايدبار: السنوات الدراسية → المواد -->
        <aside class="lg:w-72 shrink-0">
            <div class="bg-white/95 backdrop-blur rounded-2xl overflow-hidden sticky top-24 card-catalog-index border border-indigo-100/50">
                <div class="p-4 border-b border-indigo-100">
                    <h2 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                        <i class="fas fa-filter text-indigo-600"></i>
                        تصفية حسب
                    </h2>
                </div>
                <div class="p-2 max-h-[70vh] overflow-y-auto" x-data="{ openYear: {{ $openYearId ?? 'null' }} }">
                    <a href="{{ route('catalog.index') }}"
                       class="flex items-center gap-2 px-4 py-3 rounded-lg font-medium transition-colors {{ !$yearId && !$subjectId ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-th-large"></i>
                        جميع الكورسات
                    </a>
                    @foreach($years as $year)
                        <div class="mt-1">
                            <button @click="openYear = openYear === {{ $year->id }} ? null : {{ $year->id }}"
                                    class="w-full flex items-center justify-between gap-2 px-4 py-3 rounded-lg font-medium transition-colors {{ ($yearId == $year->id) ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-gray-500"></i>
                                    {{ $year->name }}
                                </span>
                                <i class="fas fa-chevron-down text-sm transition-transform" :class="openYear === {{ $year->id }} ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="openYear === {{ $year->id }}" x-transition class="mr-4 border-r-2 border-indigo-200 dark:border-indigo-800">
                                @foreach($year->academicSubjects as $sub)
                                    <a href="{{ route('catalog.index', ['year_id' => $year->id, 'subject_id' => $sub->id]) }}"
                                       class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm transition-colors {{ ($subjectId == $sub->id) ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                        <i class="fas fa-book w-4 text-center"></i>
                                        {{ $sub->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- بطاقات الكورسات -->
        <main class="flex-1 min-w-0">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">الكورسات</h1>
                <p class="text-gray-600 mt-1">اختر كورساً واشترك أو اعرض التفاصيل</p>
            </div>

            @if($courses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($courses as $course)
                        <div class="bg-white rounded-2xl overflow-hidden flex flex-col card-catalog-index">
                            <div class="h-40 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center relative">
                                @if($course->thumbnail)
                                    <img src="{{ storage_url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-graduation-cap text-white text-5xl opacity-80"></i>
                                @endif
                                @if($course->price && $course->price > 0)
                                    <div class="absolute top-3 left-3 bg-white/95 rounded-lg px-3 py-1">
                                        <span class="text-gray-900 font-bold">{{ number_format($course->price) }} ج.م</span>
                                    </div>
                                @else
                                    <div class="absolute top-3 left-3 bg-green-500/95 text-white rounded-lg px-3 py-1 font-medium">مجاني</div>
                                @endif
                                <div class="absolute top-3 right-3">
                                    @php
                                        $levelClass = match($course->level ?? '') {
                                            'beginner' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200',
                                            'intermediate' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200',
                                            'advanced' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $levelClass }}">
                                        {{ $course->level_badge['text'] ?? 'مبتدئ' }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <p class="text-xs text-gray-500 mb-1">{{ $course->academicSubject->name ?? '' }} — {{ $course->academicYear->name ?? '' }}</p>
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $course->title }}</h3>
                                <p class="text-gray-600 text-sm mb-4 flex-1 line-clamp-2">{{ Str::limit($course->description, 90) }}</p>
                                <div class="flex items-center gap-3 text-xs text-gray-500 mb-4">
                                    @if($course->duration_hours)
                                        <span><i class="fas fa-clock ml-1"></i>{{ $course->duration_hours }} ساعة</span>
                                    @endif
                                    <span><i class="fas fa-play-circle ml-1"></i>{{ $course->total_lessons ?? 0 }} درس</span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('catalog.show', $course) }}"
                                       class="flex-1 text-center py-2 px-3 rounded-xl text-sm font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                                        <i class="fas fa-eye ml-1 text-xs"></i>
                                        عرض التفاصيل
                                    </a>
                                    @auth
                                        <a href="{{ route('courses.show', $course) }}"
                                           class="flex-1 text-center py-2 px-3 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">
                                            <i class="fas fa-shopping-cart ml-1 text-xs"></i>
                                            شراء الآن
                                        </a>
                                    @else
                                        <a href="{{ route('login', ['intended' => route('courses.show', $course)]) }}"
                                           class="flex-1 text-center py-2 px-3 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">
                                            <i class="fas fa-shopping-cart ml-1 text-xs"></i>
                                            شراء الآن
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8">
                    {{ $courses->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-gray-50 rounded-xl shadow-lg border border-gray-200 p-12 text-center">
                    <i class="fas fa-graduation-cap text-5xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد كورسات</h3>
                    <p class="text-gray-600">لم يتم إضافة كورسات لهذا التصفية بعد. جرب تصفية أخرى أو عرض جميع الكورسات.</p>
                    <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 mt-4 text-indigo-600 hover:text-indigo-700 font-medium">
                        <i class="fas fa-th-large"></i>
                        عرض الكل
                    </a>
                </div>
            @endif
        </main>
    </div>
    </div>
</div>
@endsection

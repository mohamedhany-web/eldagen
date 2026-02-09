@php
    $levelClasses = match($advancedCourse->level ?? '') {
        'beginner'     => 'bg-green-100 text-green-800',
        'intermediate' => 'bg-yellow-100 text-yellow-800',
        'advanced'     => 'bg-red-100 text-red-800',
        default        => 'bg-gray-100 text-gray-800',
    };
    $levelBadgeText = $advancedCourse->level_badge['text'] ?? 'مبتدئ';
    $objectivesList = $advancedCourse->objectives ? array_filter(array_map('trim', preg_split('/[\n,،]+/', $advancedCourse->objectives))) : [];
@endphp

@extends('layouts.welcome-layout')

@section('title', $advancedCourse->title)

@push('styles')
<style>
    /* ألوان النظام + عناصر متحركة مثل الصفحة الرئيسية */
    .course-hero-gradient {
        background: linear-gradient(180deg, #eef2ff 0%, #e0e7ff 40%, #ffffff 100%);
    }
    .floating-shape-catalog {
        position: absolute;
        opacity: 0.12;
        animation: floatCatalog 8s ease-in-out infinite;
        pointer-events: none;
    }
    .floating-shape-catalog:nth-child(1) { top: 10%; right: 5%; width: 64px; height: 64px; border-radius: 50%; background: #818cf8; animation-delay: 0s; }
    .floating-shape-catalog:nth-child(2) { bottom: 25%; left: 8%; width: 48px; height: 48px; border-radius: 50%; background: #a5b4fc; animation-delay: 2s; }
    .floating-shape-catalog:nth-child(3) { top: 45%; right: 12%; width: 40px; height: 40px; background: #667eea; border-radius: 12px; animation-delay: 4s; }
    .floating-shape-catalog:nth-child(4) { bottom: 35%; right: 6%; width: 80px; height: 4px; background: linear-gradient(90deg, transparent, #764ba2, transparent); animation-delay: 1s; }
    .floating-shape-catalog:nth-child(5) { top: 30%; left: 15%; width: 56px; height: 56px; border-radius: 16px; background: #c4b5fd; animation-delay: 3s; }
    .floating-shape-catalog:nth-child(6) { bottom: 15%; right: 25%; width: 32px; height: 32px; border-radius: 50%; background: #ddd6fe; animation-delay: 5s; }
    @keyframes floatCatalog {
        0%, 100% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.12; }
        50% { transform: translateY(-24px) rotate(180deg) scale(1.05); opacity: 0.22; }
    }
    .floating-numbers-catalog {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        overflow: hidden;
    }
    .floating-number-catalog {
        position: absolute;
        color: rgba(102, 126, 234, 0.08);
        font-size: 1.5rem;
        font-weight: bold;
        animation: floatNumberCatalog 18s linear infinite;
    }
    @keyframes floatNumberCatalog {
        0% { transform: translateY(100vh) rotate(0deg) scale(0.5); opacity: 0; }
        15% { opacity: 0.6; transform: translateY(70vh) rotate(72deg) scale(0.8); }
        50% { opacity: 0.4; transform: translateY(40vh) rotate(180deg) scale(1); }
        85% { opacity: 0.2; transform: translateY(10vh) rotate(288deg) scale(0.7); }
        100% { transform: translateY(-10vh) rotate(360deg) scale(0.4); opacity: 0; }
    }
    .btn-buy-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 18px;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    .btn-buy-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.35);
    }
    .card-catalog {
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .card-catalog:hover {
        box-shadow: 0 12px 32px rgba(102, 126, 234, 0.12);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-white">
    {{-- هيرو: خلفية متدرجة + أشكال متحركة + أرقام عائمة (مثل الصفحة الرئيسية) --}}
    <section class="relative course-hero-gradient overflow-hidden pt-24 pb-10 lg:pb-14">
        <div class="floating-numbers-catalog">
            <span class="floating-number-catalog" style="left: 8%; animation-delay: 0s;">π</span>
            <span class="floating-number-catalog" style="left: 22%; animation-delay: 3s;">∞</span>
            <span class="floating-number-catalog" style="left: 38%; animation-delay: 6s;">∑</span>
            <span class="floating-number-catalog" style="left: 55%; animation-delay: 9s;">∫</span>
            <span class="floating-number-catalog" style="left: 72%; animation-delay: 12s;">θ</span>
            <span class="floating-number-catalog" style="left: 88%; animation-delay: 15s;">Δ</span>
        </div>
        <div class="floating-shape-catalog"></div>
        <div class="floating-shape-catalog"></div>
        <div class="floating-shape-catalog"></div>
        <div class="floating-shape-catalog"></div>
        <div class="floating-shape-catalog"></div>
        <div class="floating-shape-catalog"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12 items-start">
                {{-- المحتوى: breadcrumbs، badge، عنوان، وصف، إحصائيات، أزرار — على الجوال يظهر أولاً --}}
                <div class="lg:col-span-2 order-1">
                    <nav class="text-sm text-gray-500 mb-4">
                        <a href="{{ url('/') }}" class="hover:text-indigo-600 transition-colors">الرئيسية</a>
                        <span class="mx-2">/</span>
                        <a href="{{ route('catalog.index') }}" class="hover:text-indigo-600 transition-colors">الكورسات</a>
                        <span class="mx-2">/</span>
                        <span class="text-gray-700 font-medium">{{ $advancedCourse->title }}</span>
                    </nav>
                    @if($advancedCourse->is_featured)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-amber-100 text-amber-800 mb-4">
                            <i class="fas fa-star text-amber-500"></i>
                            كورس مميز
                        </span>
                    @endif
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">{{ $advancedCourse->title }}</h1>
                    <p class="text-gray-700 leading-relaxed mb-6 max-w-2xl">{{ $advancedCourse->description }}</p>

                    <div class="flex flex-wrap gap-3 mb-6">
                        <div class="text-center p-3 bg-white/90 backdrop-blur rounded-xl min-w-[90px] card-catalog border border-indigo-100/50">
                            <div class="text-xl font-bold text-indigo-600">{{ $levelBadgeText }}</div>
                            <div class="text-xs text-gray-600">المستوى</div>
                        </div>
                        @if($advancedCourse->duration_hours)
                            <div class="text-center p-3 bg-white/90 backdrop-blur rounded-xl min-w-[90px] card-catalog border border-indigo-100/50">
                                <div class="text-xl font-bold text-indigo-600">{{ $advancedCourse->duration_hours }}</div>
                                <div class="text-xs text-gray-600">ساعة</div>
                            </div>
                        @endif
                        <div class="text-center p-3 bg-white/90 backdrop-blur rounded-xl min-w-[90px] card-catalog border border-indigo-100/50">
                            <div class="text-xl font-bold text-purple-600">{{ $advancedCourse->total_lessons ?? 0 }}</div>
                            <div class="text-xs text-gray-600">درس</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @auth
                            @if($isEnrolled)
                                <a href="{{ route('my-courses.show', $advancedCourse) }}" class="btn-buy-gradient">
                                    <i class="fas fa-play"></i>
                                    ادخل للكورس
                                </a>
                            @elseif($existingOrder && $existingOrder->status == 'pending')
                                <a href="{{ route('orders.show', $existingOrder) }}" class="btn-buy-gradient">
                                    <i class="fas fa-clock"></i>
                                    عرض حالة الطلب
                                </a>
                            @else
                                <a href="{{ route('courses.show', $advancedCourse) }}" class="btn-buy-gradient">
                                    <i class="fas fa-shopping-cart"></i>
                                    شراء الآن
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login', ['intended' => route('courses.show', $advancedCourse)]) }}" class="btn-buy-gradient">
                                <i class="fas fa-shopping-cart"></i>
                                شراء الآن
                            </a>
                        @endauth
                        <a href="{{ route('catalog.index') }}" class="btn-outline inline-flex items-center gap-2 text-sm py-2.5 px-4">
                            <i class="fas fa-arrow-right text-sm"></i>
                            جميع الكورسات
                        </a>
                    </div>
                </div>

                {{-- بطاقة السعر والمميزات (ستيكي) — على الجوال تظهر بعد اسم الكورس --}}
                <div class="lg:col-span-1 order-2">
                    <div class="bg-white rounded-2xl p-5 lg:p-6 sticky top-24 card-catalog">
                        <div class="text-center mb-5">
                            @if($advancedCourse->price && $advancedCourse->price > 0)
                                <div class="text-2xl font-bold text-gray-900">{{ number_format($advancedCourse->price, 2) }} <span class="text-base text-gray-500 font-normal">ج.م</span></div>
                            @else
                                <div class="text-2xl font-bold text-indigo-600">مجاني</div>
                            @endif
                        </div>
                        <ul class="space-y-2.5 mb-5">
                            <li class="flex items-center gap-2.5 text-gray-700 text-sm">
                                <span class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-check text-indigo-600 text-xs"></i></span>
                                وصول مدى الحياة
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-700 text-sm">
                                <span class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-check text-indigo-600 text-xs"></i></span>
                                شهادة إتمام معتمدة
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-700 text-sm">
                                <span class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-check text-indigo-600 text-xs"></i></span>
                                مشاريع عملية
                            </li>
                            <li class="flex items-center gap-2.5 text-gray-700 text-sm">
                                <span class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-check text-indigo-600 text-xs"></i></span>
                                دعم مباشر
                            </li>
                        </ul>
                        @auth
                            @if($isEnrolled)
                                <a href="{{ route('my-courses.show', $advancedCourse) }}" class="w-full btn-buy-gradient justify-center">ادخل للكورس</a>
                            @elseif($existingOrder && $existingOrder->status == 'pending')
                                <a href="{{ route('orders.show', $existingOrder) }}" class="w-full btn-buy-gradient justify-center">عرض حالة الطلب</a>
                            @else
                                <a href="{{ route('courses.show', $advancedCourse) }}" class="w-full btn-buy-gradient justify-center">شراء الآن</a>
                            @endif
                        @else
                            <a href="{{ route('login', ['intended' => route('courses.show', $advancedCourse)]) }}" class="w-full btn-buy-gradient justify-center">شراء الآن</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- المحتوى السفلي: عن الكورس | ما ستعلمه | المتطلبات + سايدبار معلومات + كورسات ذات صلة --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- العمود الرئيسي: عن الكورس، ما ستعلمه، المتطلبات --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white rounded-2xl p-5 lg:p-6 card-catalog">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <span class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center"><i class="fas fa-info-circle text-indigo-600 text-sm"></i></span>
                        عن الكورس
                    </h2>
                    <p class="text-gray-700 leading-relaxed mb-4">{{ $advancedCourse->description }}</p>
                    @if(count($objectivesList) > 0)
                        <h3 class="font-bold text-gray-900 mb-2">أهداف الكورس:</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($objectivesList as $obj)
                                <span class="inline-flex px-4 py-2 rounded-xl bg-indigo-50 text-indigo-800 text-sm font-medium">{{ $obj }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if($advancedCourse->what_you_learn)
                    <div class="bg-white rounded-2xl p-5 lg:p-6 card-catalog">
                        <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center"><i class="fas fa-graduation-cap text-indigo-600 text-sm"></i></span>
                            ما ستعلمه
                        </h2>
                        <div class="inline-flex items-start gap-2.5 px-3 py-2.5 rounded-xl bg-indigo-50 text-gray-700 text-sm">
                            <i class="fas fa-check text-indigo-600 mt-0.5"></i>
                            <div class="whitespace-pre-wrap">{{ $advancedCourse->what_you_learn }}</div>
                        </div>
                    </div>
                @endif

                <div class="bg-white rounded-2xl p-5 lg:p-6 card-catalog">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <span class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center"><i class="fas fa-list text-indigo-600 text-sm"></i></span>
                        المتطلبات
                    </h2>
                    <div class="inline-flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-indigo-50 text-gray-700 text-sm">
                        <i class="fas fa-check text-indigo-600"></i>
                        <span>{{ $advancedCourse->requirements ?: 'لا توجد متطلبات مسبقة' }}</span>
                    </div>
                </div>
            </div>

            {{-- السايدبار: معلومات الكورس + كورسات ذات صلة --}}
            <div class="lg:col-span-1 space-y-5">
                <div class="bg-white rounded-2xl p-5 card-catalog">
                    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center"><i class="fas fa-info-circle text-indigo-600 text-xs"></i></span>
                        معلومات الكورس
                    </h2>
                    <div class="space-y-3">
                        @if($advancedCourse->duration_hours)
                            <div class="flex items-center gap-3 text-gray-700">
                                <span class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-clock text-indigo-600 text-sm"></i></span>
                                <span>{{ $advancedCourse->duration_hours }} ساعة</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-3 text-gray-700">
                            <span class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-book-open text-indigo-600 text-sm"></i></span>
                            <span>{{ $advancedCourse->total_lessons ?? 0 }} درس</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-700">
                            <span class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-signal text-purple-600 text-sm"></i></span>
                            <span>{{ $levelBadgeText }}</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        @if($advancedCourse->price && $advancedCourse->price > 0)
                            <div class="text-2xl font-bold text-indigo-600">{{ number_format($advancedCourse->price) }} <span class="text-base text-gray-500 font-normal">ج.م</span></div>
                        @else
                            <div class="text-2xl font-bold text-indigo-600">مجاني</div>
                        @endif
                    </div>
                    @auth
                        @if(!$isEnrolled && (!$existingOrder || $existingOrder->status != 'pending'))
                            <a href="{{ route('courses.show', $advancedCourse) }}" class="mt-3 w-full btn-buy-gradient justify-center block text-center">شراء الآن</a>
                        @endif
                    @else
                        <a href="{{ route('login', ['intended' => route('courses.show', $advancedCourse)]) }}" class="mt-3 w-full btn-buy-gradient justify-center block text-center">شراء الآن</a>
                    @endauth
                </div>

                @if(isset($relatedCourses) && $relatedCourses->isNotEmpty())
                    <div class="bg-white rounded-2xl p-5 card-catalog">
                        <h2 class="text-base font-bold text-gray-900 mb-3">كورسات ذات صلة</h2>
                        <div class="space-y-3">
                            @foreach($relatedCourses as $rel)
                                <a href="{{ route('catalog.show', $rel) }}" class="block p-3 rounded-xl border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50/50 transition-all duration-300 text-sm">
                                    <h3 class="font-bold text-gray-900 mb-1">{{ $rel->title }}</h3>
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($rel->description, 80) }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

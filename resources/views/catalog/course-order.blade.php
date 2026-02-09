@php
    $levelClasses = match($advancedCourse->level ?? '') {
        'beginner'     => 'bg-green-100 text-green-800',
        'intermediate' => 'bg-yellow-100 text-yellow-800',
        'advanced'     => 'bg-red-100 text-red-800',
        default        => 'bg-gray-100 text-gray-800',
    };
    $levelBadgeText = $advancedCourse->level_badge['text'] ?? 'مبتدئ';
@endphp

@extends('layouts.welcome-layout')

@section('title', 'طلب شراء - ' . $advancedCourse->title)

@push('styles')
<style>
    .order-hero-gradient {
        background: linear-gradient(180deg, #eef2ff 0%, #e0e7ff 40%, #ffffff 100%);
    }
    .floating-shape-order {
        position: absolute;
        opacity: 0.12;
        animation: floatOrder 8s ease-in-out infinite;
        pointer-events: none;
    }
    .floating-shape-order:nth-child(1) { top: 10%; right: 5%; width: 64px; height: 64px; border-radius: 50%; background: #818cf8; animation-delay: 0s; }
    .floating-shape-order:nth-child(2) { bottom: 25%; left: 8%; width: 48px; height: 48px; border-radius: 50%; background: #a5b4fc; animation-delay: 2s; }
    .floating-shape-order:nth-child(3) { top: 45%; right: 12%; width: 40px; height: 40px; background: #667eea; border-radius: 12px; animation-delay: 4s; }
    .floating-shape-order:nth-child(4) { bottom: 35%; right: 6%; width: 80px; height: 4px; background: linear-gradient(90deg, transparent, #764ba2, transparent); animation-delay: 1s; }
    .floating-shape-order:nth-child(5) { top: 30%; left: 15%; width: 56px; height: 56px; border-radius: 16px; background: #c4b5fd; animation-delay: 3s; }
    @keyframes floatOrder {
        0%, 100% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.12; }
        50% { transform: translateY(-24px) rotate(180deg) scale(1.05); opacity: 0.22; }
    }
    .floating-numbers-order {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        pointer-events: none;
        overflow: hidden;
    }
    .floating-number-order {
        position: absolute;
        color: rgba(102, 126, 234, 0.08);
        font-size: 1.5rem;
        font-weight: bold;
        animation: floatNumberOrder 18s linear infinite;
    }
    @keyframes floatNumberOrder {
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
    .card-order {
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .card-order:hover {
        box-shadow: 0 12px 32px rgba(102, 126, 234, 0.12);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-white">
    <section class="relative order-hero-gradient overflow-hidden pt-24 pb-10 lg:pb-14">
        <div class="floating-numbers-order">
            <span class="floating-number-order" style="left: 8%; animation-delay: 0s;">π</span>
            <span class="floating-number-order" style="left: 22%; animation-delay: 3s;">∞</span>
            <span class="floating-number-order" style="left: 38%; animation-delay: 6s;">∑</span>
            <span class="floating-number-order" style="left: 55%; animation-delay: 9s;">∫</span>
            <span class="floating-number-order" style="left: 72%; animation-delay: 12s;">θ</span>
            <span class="floating-number-order" style="left: 88%; animation-delay: 15s;">Δ</span>
        </div>
        <div class="floating-shape-order"></div>
        <div class="floating-shape-order"></div>
        <div class="floating-shape-order"></div>
        <div class="floating-shape-order"></div>
        <div class="floating-shape-order"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <nav class="text-sm text-gray-500 mb-4">
                <a href="{{ url('/') }}" class="hover:text-indigo-600 transition-colors">الرئيسية</a>
                <span class="mx-2">/</span>
                <a href="{{ route('catalog.index') }}" class="hover:text-indigo-600 transition-colors">الكورسات</a>
                <span class="mx-2">/</span>
                <a href="{{ route('catalog.show', $advancedCourse) }}" class="hover:text-indigo-600 transition-colors">{{ Str::limit($advancedCourse->title, 30) }}</a>
                <span class="mx-2">/</span>
                <span class="text-gray-700 font-medium">طلب الشراء</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12 items-start">
                {{-- تفاصيل الكورس --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl overflow-hidden card-order">
                        <div class="h-48 lg:h-56 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center relative">
                            @if($advancedCourse->thumbnail)
                                <img src="{{ storage_url($advancedCourse->thumbnail) }}" alt="{{ $advancedCourse->title }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-graduation-cap text-white text-6xl opacity-80"></i>
                            @endif
                            <span class="absolute top-4 right-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $levelClasses }}">{{ $levelBadgeText }}</span>
                        </div>
                        <div class="p-5 lg:p-6">
                            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3">{{ $advancedCourse->title }}</h1>
                            <p class="text-gray-700 leading-relaxed mb-4">{{ Str::limit($advancedCourse->description, 200) }}</p>
                            <div class="flex flex-wrap gap-3">
                                @if($advancedCourse->duration_hours)
                                    <div class="text-center p-3 bg-indigo-50 rounded-xl min-w-[80px]">
                                        <div class="text-lg font-bold text-indigo-600">{{ $advancedCourse->duration_hours }}</div>
                                        <div class="text-xs text-gray-600">ساعة</div>
                                    </div>
                                @endif
                                <div class="text-center p-3 bg-purple-50 rounded-xl min-w-[80px]">
                                    <div class="text-lg font-bold text-purple-600">{{ $advancedCourse->total_lessons ?? 0 }}</div>
                                    <div class="text-xs text-gray-600">درس</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-600"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                            {{ session('error') }}
                        </div>
                    @endif
                </div>

                {{-- بطاقة الطلب والنموذج --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl p-5 lg:p-6 sticky top-24 card-order">
                        <div class="text-center mb-5">
                            @if($advancedCourse->price && $advancedCourse->price > 0)
                                <div class="text-2xl font-bold text-gray-900">{{ number_format($advancedCourse->price, 2) }} <span class="text-base text-gray-500 font-normal">ج.م</span></div>
                            @else
                                <div class="text-2xl font-bold text-indigo-600">مجاني</div>
                            @endif
                        </div>

                        @if($isEnrolled)
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                                <div class="flex items-center gap-2 text-green-800">
                                    <i class="fas fa-check-circle text-green-600"></i>
                                    <span class="font-medium">أنت مسجل في هذا الكورس</span>
                                </div>
                            </div>
                            <a href="{{ route('my-courses.show', $advancedCourse) }}" class="w-full btn-buy-gradient justify-center">ادخل للكورس</a>
                        @elseif($existingOrder && $existingOrder->status == 'pending')
                            @if(session('success'))
                                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 flex items-center gap-2">
                                    <i class="fas fa-check-circle text-green-600"></i>
                                    {{ session('success') }}
                                </div>
                            @endif
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                                <div class="flex items-center gap-2 text-amber-800">
                                    <i class="fas fa-clock text-amber-600"></i>
                                    <span class="font-medium">طلبك قيد المراجعة</span>
                                </div>
                                <p class="text-sm text-amber-700 mt-1">سيتم مراجعة طلبك والرد عليك قريباً</p>
                            </div>
                            <a href="{{ route('orders.show', $existingOrder) }}" class="w-full btn-buy-gradient justify-center">
                                <i class="fas fa-clock"></i>
                                عرض حالة الطلب
                            </a>
                        @elseif($existingOrder && $existingOrder->status == 'rejected')
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                                <div class="flex items-center gap-2 text-red-800">
                                    <i class="fas fa-times-circle text-red-600"></i>
                                    <span class="font-medium">تم رفض طلبك السابق</span>
                                </div>
                                <p class="text-sm text-red-700 mt-1">يمكنك تقديم طلب جديد أدناه</p>
                            </div>
                            @include('catalog.partials.order-form')
                        @else
                            @include('catalog.partials.order-form')
                        @endif

                        <a href="{{ route('catalog.show', $advancedCourse) }}" class="mt-4 w-full btn-outline justify-center text-center inline-flex">
                            <i class="fas fa-arrow-right text-sm"></i>
                            العودة لتفاصيل الكورس
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

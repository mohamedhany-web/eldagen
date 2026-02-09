@php
    $course = $order->course;
@endphp

@extends('layouts.welcome-layout')

@section('title', 'تفاصيل الطلب #' . $order->id)

@push('styles')
<style>
    .order-show-gradient {
        background: linear-gradient(180deg, #eef2ff 0%, #e0e7ff 40%, #ffffff 100%);
    }
    .floating-shape-order-show {
        position: absolute;
        opacity: 0.12;
        animation: floatOrderShow 8s ease-in-out infinite;
        pointer-events: none;
    }
    .floating-shape-order-show:nth-child(1) { top: 15%; right: 5%; width: 56px; height: 56px; border-radius: 50%; background: #818cf8; animation-delay: 0s; }
    .floating-shape-order-show:nth-child(2) { bottom: 30%; left: 8%; width: 40px; height: 40px; border-radius: 10px; background: #a5b4fc; animation-delay: 2s; }
    .floating-shape-order-show:nth-child(3) { top: 50%; right: 15%; width: 32px; height: 32px; border-radius: 50%; background: #667eea; animation-delay: 4s; }
    @keyframes floatOrderShow {
        0%, 100% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.12; }
        50% { transform: translateY(-20px) rotate(180deg) scale(1.05); opacity: 0.2; }
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
    .card-order-show {
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .card-order-show:hover {
        box-shadow: 0 12px 32px rgba(102, 126, 234, 0.12);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-white">
    <section class="relative order-show-gradient overflow-hidden pt-20 sm:pt-24 lg:pt-28 pb-10 lg:pb-16 xl:pb-20">
        <div class="floating-shape-order-show"></div>
        <div class="floating-shape-order-show"></div>
        <div class="floating-shape-order-show"></div>

        <div class="max-w-4xl xl:max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 xl:px-10 relative z-10">
            {{-- مسار التنقل + عنوان --}}
            <nav class="text-sm text-gray-500 mb-4 lg:mb-6 flex flex-wrap items-center gap-x-2 gap-y-1">
                <a href="{{ url('/') }}" class="hover:text-indigo-600 transition-colors">الرئيسية</a>
                <span class="text-gray-400">/</span>
                @auth
                <a href="{{ route('orders.index') }}" class="hover:text-indigo-600 transition-colors">طلباتي</a>
                <span class="text-gray-400">/</span>
                @endauth
                <a href="{{ route('catalog.index') }}" class="hover:text-indigo-600 transition-colors">الكورسات</a>
                <span class="text-gray-400">/</span>
                <a href="{{ route('catalog.show', $course) }}" class="hover:text-indigo-600 transition-colors">{{ Str::limit($course->title, 30) }}</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800 font-semibold">الطلب #{{ $order->id }}</span>
            </nav>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-6 lg:mb-8">تفاصيل الطلب #{{ $order->id }}</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 xl:gap-10">
                {{-- تفاصيل الطلب والكورس --}}
                <div class="lg:col-span-2 space-y-6 lg:space-y-8">
                    <div class="bg-white rounded-2xl p-5 sm:p-6 lg:p-7 xl:p-8 card-order-show shadow-lg shadow-gray-200/50">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-4 lg:mb-5 flex items-center gap-2">
                            <span class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-book-open text-indigo-600 text-sm lg:text-base"></i></span>
                            معلومات الكورس
                        </h2>
                        <div class="flex flex-col sm:flex-row gap-4 lg:gap-6">
                            <div class="w-full sm:w-28 h-28 lg:w-36 lg:h-36 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0 overflow-hidden shadow-md">
                                @if($course->thumbnail)
                                    <img src="{{ storage_url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-graduation-cap text-white text-3xl lg:text-4xl"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-1 lg:mb-2">{{ $course->title }}</h3>
                                <p class="text-sm lg:text-base text-gray-600 mb-2">
                                    {{ $course->academicYear->name ?? '' }} — {{ $course->academicSubject->name ?? '' }}
                                </p>
                                @if($course->description)
                                    <p class="text-sm lg:text-base text-gray-700 line-clamp-3">{{ Str::limit($course->description, 150) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-5 sm:p-6 lg:p-7 xl:p-8 card-order-show shadow-lg shadow-gray-200/50">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-4 lg:mb-5 flex items-center gap-2">
                            <span class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-credit-card text-indigo-600 text-sm lg:text-base"></i></span>
                            تفاصيل الدفع
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-5">
                            <div class="p-4 lg:p-5 bg-indigo-50 rounded-xl border border-indigo-100">
                                <div class="text-xs font-medium text-gray-600 mb-1">المبلغ</div>
                                <div class="text-lg lg:text-xl font-bold text-indigo-600">
                                    @if($order->payment_method == 'code') تم الدفع بالكود
                                    @else {{ number_format($order->amount) }} ج.م
                                    @endif
                                </div>
                            </div>
                            <div class="p-4 lg:p-5 bg-indigo-50 rounded-xl border border-indigo-100">
                                <div class="text-xs font-medium text-gray-600 mb-1">طريقة الدفع</div>
                                <div class="font-semibold text-gray-900">
                                    @if($order->payment_method == 'code')
                                        كود التفعيل
                                        @if($order->activationCode)
                                            <span class="font-mono text-indigo-600 block mt-1">{{ $order->activationCode->code }}</span>
                                        @endif
                                    @elseif($order->payment_method == 'bank_transfer') تحويل بنكي
                                    @elseif($order->payment_method == 'cash') نقدي
                                    @else أخرى
                                    @endif
                                </div>
                            </div>
                            <div class="p-4 lg:p-5 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="text-xs font-medium text-gray-600 mb-1">تاريخ الطلب</div>
                                <div class="font-semibold text-gray-900">{{ $order->created_at->format('d/m/Y - H:i') }}</div>
                            </div>
                            @if($order->approved_at)
                                <div class="p-4 lg:p-5 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="text-xs font-medium text-gray-600 mb-1">تاريخ الموافقة</div>
                                    <div class="font-semibold text-gray-900">{{ $order->approved_at->format('d/m/Y - H:i') }}</div>
                                </div>
                            @endif
                        </div>
                        @if($order->notes)
                            <div class="mt-4 lg:mt-5 p-4 lg:p-5 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="text-xs font-medium text-gray-600 mb-1">ملاحظات</div>
                                <p class="text-gray-900 text-sm lg:text-base">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>

                    @if($order->payment_proof)
                        <div class="bg-white rounded-2xl p-5 sm:p-6 lg:p-7 xl:p-8 card-order-show shadow-lg shadow-gray-200/50">
                            <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-4 lg:mb-5 flex items-center gap-2">
                                <span class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-receipt text-indigo-600 text-sm lg:text-base"></i></span>
                                إيصال الدفع
                            </h2>
                            <div class="text-center">
                                <img src="{{ storage_url($order->payment_proof) }}" alt="إيصال الدفع"
                                     class="max-w-full h-auto rounded-xl shadow-lg cursor-pointer mx-auto max-h-72 lg:max-h-96 object-contain border border-gray-100 hover:shadow-xl transition-shadow"
                                     onclick="openImageModal(this.src)">
                                <p class="text-sm text-gray-500 mt-3 lg:mt-4">اضغط على الصورة لعرضها بحجم أكبر</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- حالة الطلب --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl p-5 sm:p-6 lg:p-7 xl:p-8 sticky top-20 lg:top-24 card-order-show shadow-lg shadow-gray-200/50 lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto">
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-4 lg:mb-5 flex items-center gap-2">
                            <span class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-info-circle text-indigo-600 text-sm lg:text-base"></i></span>
                            حالة الطلب
                        </h2>
                        <div class="text-center mb-6 lg:mb-8">
                            <div class="w-20 h-20 lg:w-24 lg:h-24 mx-auto mb-3 lg:mb-4 rounded-full flex items-center justify-center
                                @if($order->status == 'pending') bg-amber-100
                                @elseif($order->status == 'approved') bg-green-100
                                @else bg-red-100
                                @endif">
                                <i class="fas
                                    @if($order->status == 'pending') fa-clock text-amber-600 text-2xl
                                    @elseif($order->status == 'approved') fa-check text-green-600 text-2xl
                                    @else fa-times text-red-600 text-2xl
                                    @endif"></i>
                            </div>
                            <div class="text-xl lg:text-2xl font-bold
                                @if($order->status == 'pending') text-amber-600
                                @elseif($order->status == 'approved') text-green-600
                                @else text-red-600
                                @endif">
                                {{ $order->status_text }}
                            </div>
                        </div>

                        @if($order->status == 'pending')
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 lg:p-5 mb-4 lg:mb-5">
                                <p class="text-sm lg:text-base text-amber-800">
                                    <i class="fas fa-info-circle ml-1"></i>
                                    طلبك قيد المراجعة من قبل الإدارة. سيتم الرد عليك قريباً.
                                </p>
                            </div>
                        @elseif($order->status == 'approved')
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4 lg:p-5 mb-4 lg:mb-5">
                                <p class="text-sm lg:text-base text-green-800">
                                    <i class="fas fa-check-circle ml-1"></i>
                                    تمت الموافقة على طلبك! يمكنك الآن الدخول للكورس.
                                </p>
                            </div>
                            <a href="{{ route('my-courses.show', $course) }}" class="w-full btn-buy-gradient justify-center py-3 lg:py-3.5 text-base">
                                <i class="fas fa-play"></i>
                                ادخل للكورس
                            </a>
                        @else
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4 lg:p-5 mb-4 lg:mb-5">
                                <p class="text-sm lg:text-base text-red-800">
                                    <i class="fas fa-exclamation-circle ml-1"></i>
                                    تم رفض طلبك. يمكنك تقديم طلب جديد أو التواصل مع الإدارة.
                                </p>
                            </div>
                            <a href="{{ route('courses.show', $course) }}" class="w-full btn-buy-gradient justify-center py-3 lg:py-3.5 text-base">
                                <i class="fas fa-shopping-cart"></i>
                                تقديم طلب جديد
                            </a>
                        @endif

                        @if($order->approver)
                            <div class="mt-4 lg:mt-5 pt-4 lg:pt-5 border-t border-gray-200">
                                <p class="text-sm text-gray-500">تمت المراجعة بواسطة:</p>
                                <p class="font-semibold text-gray-900">{{ $order->approver->name }}</p>
                            </div>
                        @endif

                        <div class="mt-4 lg:mt-5 space-y-3">
                            @auth
                            <a href="{{ route('orders.index') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 lg:py-3 px-4 rounded-xl border-2 border-gray-200 text-gray-700 font-medium hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
                                <i class="fas fa-list"></i>
                                طلباتي
                            </a>
                            @endauth
                            <a href="{{ route('catalog.index') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 lg:py-3 px-4 rounded-xl border-2 border-indigo-200 text-indigo-700 font-medium hover:bg-indigo-50 transition-colors">
                                <i class="fas fa-arrow-right text-sm"></i>
                                جميع الكورسات
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Modal لعرض الصورة --}}
<div id="imageModal" class="fixed inset-0 bg-black/80 z-50 p-4 hidden" style="display: none;" onclick="closeImageModal()">
    <div class="min-h-full flex items-center justify-center">
        <img id="modalImage" src="" alt="إيصال الدفع" class="max-w-full max-h-[85vh] lg:max-h-[90vh] object-contain rounded-xl shadow-2xl cursor-zoom-out" onclick="event.stopPropagation()">
    </div>
</div>

<script>
function openImageModal(src) {
    var modal = document.getElementById('imageModal');
    document.getElementById('modalImage').src = src;
    modal.style.display = 'flex';
    modal.classList.add('flex', 'items-center', 'justify-center');
    document.body.style.overflow = 'hidden';
}
function closeImageModal() {
    var modal = document.getElementById('imageModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}
</script>
@endsection

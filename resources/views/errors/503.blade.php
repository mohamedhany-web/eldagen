@extends('errors::layout')

@section('title', 'المنصة قيد الصيانة')

@section('content')
    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl flex items-center justify-center text-white bg-amber-500/90">
        <i class="fas fa-tools text-4xl"></i>
    </div>
    <h1 class="text-4xl sm:text-5xl font-bold text-white mb-2">503</h1>
    <p class="text-xl font-semibold text-white/95 mb-2">المنصة قيد الصيانة</p>
    <p class="text-white/70 text-sm mb-8">نقوم بتحديث المنصة لخدمتك بشكل أفضل. سنعود قريباً.</p>
    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-white transition-all duration-300 hover:scale-105" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);">
        <i class="fas fa-redo"></i>
        إعادة المحاولة
    </a>
@endsection

@extends('errors::layout')

@section('title', 'انتهت صلاحية الصفحة')

@section('content')
    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl flex items-center justify-center text-white bg-amber-500/90">
        <i class="fas fa-clock text-4xl"></i>
    </div>
    <h1 class="text-4xl sm:text-5xl font-bold text-white mb-2">419</h1>
    <p class="text-xl font-semibold text-white/95 mb-2">انتهت صلاحية الصفحة</p>
    <p class="text-white/70 text-sm mb-8">انتهت جلستك أو انتهت صلاحية النموذج. حدّث الصفحة ثم أعد الإرسال.</p>
    <div class="flex flex-wrap gap-3 justify-center">
        <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-white transition-all duration-300 hover:scale-105" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);">
            <i class="fas fa-arrow-right"></i>
            العودة والمحاولة مرة أخرى
        </a>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-white/90 bg-white/10 hover:bg-white/20 transition-all duration-300 border border-white/20">
            <i class="fas fa-home"></i>
            الصفحة الرئيسية
        </a>
    </div>
@endsection

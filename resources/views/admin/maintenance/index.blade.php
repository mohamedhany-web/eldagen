@extends('layouts.app')

@section('title', 'وضع الصيانة')
@section('header', 'وضع الصيانة')

@section('content')
<div class="space-y-6">
    <nav class="text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
        <span class="mx-2">/</span>
        <span>وضع الصيانة</span>
    </nav>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="flex items-center justify-center w-20 h-20 mx-auto mb-6 rounded-2xl
                    {{ $isMaintenance ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-green-100 dark:bg-green-900/30' }}">
                    @if($isMaintenance)
                        <i class="fas fa-tools text-4xl text-amber-600 dark:text-amber-400"></i>
                    @else
                        <i class="fas fa-check-circle text-4xl text-green-600 dark:text-green-400"></i>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-center text-gray-900 dark:text-white mb-2">
                    @if($isMaintenance)
                        الموقع حالياً تحت الصيانة
                    @else
                        الموقع يعمل بشكل طبيعي
                    @endif
                </h2>
                <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
                    @if($isMaintenance)
                        الصفحة الرئيسية وجميع عمليات الموقع متوقفة للزوار. لوحة الأدمن تعمل بشكل طبيعي. اضغط «تشغيل الموقع» لإعادة الموقع للعمل.
                    @else
                        يمكنك تفعيل وضع الصيانة لإيقاف الموقع أمام الزوار مع الاستمرار في استخدام لوحة الأدمن.
                    @endif
                </p>

                <div class="mb-8 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-600">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        <i class="fas fa-key ml-2"></i>
                        رابط الدخول أثناء الصيانة (ثابت — من يمتلكه فقط يمكنه الدخول لتسجيل الدخول)
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">افتح هذا الرابط عند تفعيل الصيانة لتسجيل الدخول وإدارة المنصة. لا توكن — الرابط نفسه هو الصلاحية.</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <input type="text" readonly value="{{ $adminEntryUrl }}" class="flex-1 min-w-0 px-3 py-2 text-sm rounded-lg bg-white dark:bg-gray-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200 font-mono" id="admin-entry-url">
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('admin-entry-url').value); this.textContent='تم النسخ'; setTimeout(() => this.innerHTML='<i class=\'fas fa-copy ml-1\'></i> نسخ الرابط', 2000)" class="shrink-0 inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700">
                            <i class="fas fa-copy ml-1"></i> نسخ الرابط
                        </button>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @if($isMaintenance)
                        <form action="{{ route('admin.maintenance.disable') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl font-semibold text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800 transition-colors">
                                <i class="fas fa-play"></i>
                                تشغيل الموقع
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.maintenance.enable') }}" method="POST" class="inline" onsubmit="return confirm('تفعيل وضع الصيانة؟ سيتم إيقاف الموقع للزوار وستبقى لوحة الأدمن تعمل.');">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl font-semibold text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:ring-amber-300 dark:focus:ring-amber-800 transition-colors">
                                <i class="fas fa-tools"></i>
                                تفعيل وضع الصيانة
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

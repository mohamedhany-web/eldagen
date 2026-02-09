@extends('layouts.focus')

@section('title', 'حسابك موقوف')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="bg-red-600 text-white px-6 py-4 text-center">
            <i class="fas fa-ban text-4xl mb-2"></i>
            <h1 class="text-xl font-bold">حسابك موقوف</h1>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-gray-700 dark:text-gray-300 text-center">
                تم تعليق حسابك بسبب مخالفة قواعد استخدام المنصة.
            </p>
            @if(auth()->user()->suspension_reason)
                <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                    سبب التعليق: 
                    @if(auth()->user()->suspension_reason === 'screenshot')
                        محاولة تصوير الشاشة (سكرين شوت) أو تسجيل محتوى الفيديو.
                    @elseif(auth()->user()->suspension_reason === 'recording')
                        محاولة تسجيل الشاشة (سكرين ريكورد) أثناء مشاهدة المحتوى.
                    @else
                        مخالفة قواعد الاستخدام.
                    @endif
                </p>
            @endif
            <p class="text-gray-600 dark:text-gray-400 text-center text-sm">
                للاستفسار أو طلب إعادة تفعيل الحساب، تواصل مع إدارة المنصة.
            </p>
            <div class="pt-4 flex flex-col gap-3">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full py-3 px-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-xl font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        <i class="fas fa-sign-out-alt ml-2"></i>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

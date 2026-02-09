@extends('layouts.welcome-layout')

@section('title', 'تقارير ولي الأمر')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-16 bg-gradient-to-b from-indigo-50 to-white">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-friends text-3xl text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">تقارير ولي الأمر</h1>
                <p class="text-white/90 mt-2 text-sm">أدخل رقم جوالك المرتبط بملف ابنك/ابنتك لمتابعة التقارير الكاملة على المنصة</p>
            </div>
            <div class="p-6">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 text-green-800 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-3 bg-amber-50 text-amber-800 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 text-red-800 rounded-lg text-sm">
                        @foreach($errors->all() as $err) {{ $err }} @endforeach
                    </div>
                @endif
                <form action="{{ route('parent-report.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">رقم جوال ولي الأمر</label>
                        <input type="tel"
                               name="phone"
                               id="phone"
                               value="{{ old('phone') }}"
                               placeholder="01xxxxxxxxx"
                               maxlength="11"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               required
                               dir="ltr"
                               inputmode="numeric">
                        <p class="mt-1 text-xs text-gray-500">رقم الجوال المسجل في ملف الطالب كجوال ولي الأمر (11 رقماً يبدأ بـ 01)</p>
                    </div>
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg">
                        <i class="fas fa-chart-line ml-2"></i>
                        عرض التقارير
                    </button>
                </form>
            </div>
        </div>
        <p class="text-center text-gray-500 text-sm mt-6">
            <a href="{{ url('/') }}" class="text-indigo-600 hover:underline">العودة للرئيسية</a>
        </p>
    </div>
</div>
@endsection

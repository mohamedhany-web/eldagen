@extends('layouts.app')

@section('title', 'البروفايل الشخصي')
@section('header', 'البروفايل الشخصي')

@section('content')
<div class="w-full min-w-0 min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-none px-3 sm:px-4 md:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        @if($errors->any())
            <div class="mb-4 sm:mb-6 p-3 sm:p-4 rounded-xl bg-red-100 dark:bg-red-900/40 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 text-sm sm:text-base">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- الهيدر (مختصر على الموبايل) -->
        <div class="mb-4 sm:mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">البروفايل الشخصي</h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة معلوماتك الشخصية</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 w-full">
            <!-- بطاقة المستخدم -->
            <div class="w-full lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl sm:rounded-2xl p-4 sm:p-6 w-full">
                    <div class="text-center">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-primary-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-2xl sm:text-3xl font-bold mx-auto mb-3 sm:mb-4 shadow-lg">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white break-words">{{ $user->name }}</h2>
                        @if($user->phone)
                            <a href="tel:{{ $user->phone }}" class="block text-gray-600 dark:text-gray-400 text-sm sm:text-base mt-1 hover:text-primary-600 dark:hover:text-primary-400">{{ $user->phone }}</a>
                        @endif
                        @if($user->email)
                            <a href="mailto:{{ $user->email }}" class="block text-gray-600 dark:text-gray-400 text-sm sm:text-base mt-1 truncate hover:text-primary-600 dark:hover:text-primary-400">{{ $user->email }}</a>
                        @endif
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs sm:text-sm font-medium
                                @if($user->role == 'student') bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200
                                @elseif($user->role == 'teacher') bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200
                                @elseif($user->role == 'admin') bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-200
                                @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                @endif">
                                @if($user->role == 'student') طالب
                                @elseif($user->role == 'teacher') معلم
                                @elseif($user->role == 'admin') مدير
                                @else مستخدم
                                @endif
                            </span>
                        </div>
                        <p class="mt-4 text-xs sm:text-sm text-gray-500 dark:text-gray-400">عضو منذ: {{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- نموذج التحديث -->
            <div class="w-full lg:col-span-2 min-w-0">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl sm:rounded-2xl p-4 sm:p-6 lg:p-8 w-full">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 sm:mb-6">تحديث البيانات</h3>

                    <form method="POST" action="{{ route('profile.update') }}" class="w-full">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div class="sm:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الاسم</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white placeholder-gray-400">
                                @error('name')
                                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">رقم الهاتف</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                                       class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white placeholder-gray-400"
                                       inputmode="tel">
                                @error('phone')
                                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني (اختياري)</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white placeholder-gray-400"
                                       inputmode="email">
                                @error('email')
                                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- تغيير كلمة المرور -->
                        <div class="mt-6 sm:mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white mb-4">تغيير كلمة المرور</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                                <div class="sm:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">كلمة المرور الحالية</label>
                                    <input type="password" name="current_password" autocomplete="current-password"
                                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                    @error('current_password')
                                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="sm:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">كلمة المرور الجديدة</label>
                                    <input type="password" name="password" autocomplete="new-password"
                                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                    @error('password')
                                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="sm:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تأكيد كلمة المرور</label>
                                    <input type="password" name="password_confirmation" autocomplete="new-password"
                                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-2">اترك الحقول فارغة إذا كنت لا تريد تغيير كلمة المرور</p>
                        </div>

                        <!-- أزرار -->
                        <div class="mt-6 sm:mt-8 flex flex-col-reverse sm:flex-row gap-3 sm:gap-4">
                            <button type="submit"
                                    class="w-full sm:w-auto order-2 sm:order-1 bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl font-medium transition-colors shadow-md active:scale-[0.98]">
                                <i class="fas fa-save ml-2"></i>
                                حفظ التغييرات
                            </button>
                            <a href="{{ route('dashboard') }}"
                               class="w-full sm:w-auto order-1 sm:order-2 text-center bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-6 py-3 rounded-xl font-medium transition-colors">
                                <i class="fas fa-arrow-right ml-2"></i>
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'الإعدادات')
@section('header', 'الإعدادات')

@section('content')
<div class="w-full min-w-0 min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-none px-3 sm:px-4 md:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        <div class="mb-4 sm:mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">الإعدادات</h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة إعدادات الحساب والتفضيلات</p>
        </div>

        <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
            @csrf

            <!-- إعدادات العرض -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl sm:rounded-2xl p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-palette ml-2 text-primary-500"></i>
                    إعدادات العرض
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المظهر</label>
                        <select name="theme" class="w-full px-3 sm:px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="light" {{ ($preferences['theme'] ?? 'auto') === 'light' ? 'selected' : '' }}>فاتح</option>
                            <option value="dark" {{ ($preferences['theme'] ?? 'auto') === 'dark' ? 'selected' : '' }}>داكن</option>
                            <option value="auto" {{ ($preferences['theme'] ?? 'auto') === 'auto' ? 'selected' : '' }}>تلقائي (حسب الجهاز)</option>
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">يُطبَّق المظهر المحفوظ عند فتح الصفحة التالية</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اللغة</label>
                        <select name="language" class="w-full px-3 sm:px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="ar" {{ ($preferences['language'] ?? 'ar') === 'ar' ? 'selected' : '' }}>العربية</option>
                            <option value="en" {{ ($preferences['language'] ?? 'ar') === 'en' ? 'selected' : '' }}>English</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- إعدادات الإشعارات -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl sm:rounded-2xl p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-bell ml-2 text-primary-500"></i>
                    إعدادات الإشعارات
                </h2>
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">إشعارات الكورسات الجديدة</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">تلقي إشعار عند إضافة كورسات جديدة</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="hidden" name="notify_courses" value="0">
                            <input type="checkbox" name="notify_courses" value="1" {{ ($preferences['notify_courses'] ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">تفعيل</span>
                        </label>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">إشعارات الطلبات</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">تلقي إشعار عند تحديث حالة طلباتك</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="hidden" name="notify_orders" value="0">
                            <input type="checkbox" name="notify_orders" value="1" {{ ($preferences['notify_orders'] ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">تفعيل</span>
                        </label>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">إشعارات الامتحانات</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">تلقي تذكير قبل مواعيد الامتحانات</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="hidden" name="notify_exams" value="0">
                            <input type="checkbox" name="notify_exams" value="1" {{ ($preferences['notify_exams'] ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">تفعيل</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- إعدادات الخصوصية -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl sm:rounded-2xl p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-shield-alt ml-2 text-primary-500"></i>
                    إعدادات الخصوصية
                </h2>
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">إظهار التقدم للمعلمين</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">السماح للمعلمين برؤية تقدمك في الكورسات</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="hidden" name="show_progress" value="0">
                            <input type="checkbox" name="show_progress" value="1" {{ ($preferences['show_progress'] ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">تفعيل</span>
                        </label>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">إظهار آخر النشاط</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">إظهار آخر نشاط لك في المنصة (للإدارة فقط)</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="hidden" name="show_activity" value="0">
                            <input type="checkbox" name="show_activity" value="1" {{ ($preferences['show_activity'] ?? false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-700 dark:text-gray-300">تفعيل</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- حفظ -->
            <div class="flex flex-col-reverse sm:flex-row gap-3 sm:gap-4">
                <button type="submit"
                        class="w-full sm:w-auto bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl font-medium transition-colors shadow-md">
                    <i class="fas fa-save ml-2"></i>
                    حفظ جميع الإعدادات
                </button>
                <a href="{{ route('dashboard') }}"
                   class="w-full sm:w-auto text-center bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-6 py-3 rounded-xl font-medium transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

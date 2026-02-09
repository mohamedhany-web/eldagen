@extends('layouts.app')

@section('title', 'تعديل السنة الدراسية')
@section('header', 'تعديل السنة الدراسية')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <!-- الهيدر -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل سنة دراسية</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $academicYear->name }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.academic-years.show', $academicYear) }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-eye mr-2"></i>
                            عرض
                        </a>
                        <a href="{{ route('admin.academic-years.index') }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-arrow-right mr-2"></i>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج التعديل -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg">
            <form method="POST" action="{{ route('admin.academic-years.update', $academicYear) }}">
                @csrf
                @method('PUT')
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- اسم السنة الدراسية -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                اسم السنة الدراسية <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   value="{{ old('name', $academicYear->name) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="مثال: الصف الأول الثانوي">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- رمز السنة الدراسية -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                رمز السنة الدراسية <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" id="code"
                                   value="{{ old('code', $academicYear->code) }}" required maxlength="10"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="مثال: AY1">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                رمز فريد قصير (حروف وأرقام، مثل AY1 أو أول ثانوي)
                            </p>
                        </div>

                        <!-- الوصف -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                الوصف
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                      placeholder="وصف مختصر للسنة الدراسية (اختياري)">{{ old('description', $academicYear->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- اللون والأيقونة -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    اللون
                                </label>
                                <input type="color" name="color" id="color"
                                       value="{{ old('color', $academicYear->color ?? '#3B82F6') }}"
                                       class="w-full h-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('color')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    الأيقونة
                                </label>
                                <select name="icon" id="icon" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="fas fa-calendar-alt" {{ old('icon', $academicYear->icon) == 'fas fa-calendar-alt' ? 'selected' : '' }}>📅 تقويم</option>
                                    <option value="fas fa-graduation-cap" {{ old('icon', $academicYear->icon) == 'fas fa-graduation-cap' ? 'selected' : '' }}>🎓 تخرج</option>
                                    <option value="fas fa-school" {{ old('icon', $academicYear->icon) == 'fas fa-school' ? 'selected' : '' }}>🏫 مدرسة</option>
                                    <option value="fas fa-book" {{ old('icon', $academicYear->icon) == 'fas fa-book' ? 'selected' : '' }}>📚 كتاب</option>
                                    <option value="fas fa-user-graduate" {{ old('icon', $academicYear->icon) == 'fas fa-user-graduate' ? 'selected' : '' }}>👨‍🎓 طالب</option>
                                </select>
                                @error('icon')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- ترتيب العرض -->
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                ترتيب العرض
                            </label>
                            <input type="number" name="order" id="order"
                                   value="{{ old('order', $academicYear->order ?? 0) }}" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('order')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- تواريخ البدء والانتهاء -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    تاريخ البدء
                                </label>
                                <input type="date" name="start_date" id="start_date"
                                       value="{{ old('start_date', $academicYear->start_date ? \Carbon\Carbon::parse($academicYear->start_date)->format('Y-m-d') : '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    تاريخ الانتهاء
                                </label>
                                <input type="date" name="end_date" id="end_date"
                                       value="{{ old('end_date', $academicYear->end_date ? \Carbon\Carbon::parse($academicYear->end_date)->format('Y-m-d') : '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- حالة السنة -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', $academicYear->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                                <label for="is_active" class="mr-2 block text-sm text-gray-700 dark:text-gray-300">
                                    السنة الدراسية نشطة
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 rounded-b-lg">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.academic-years.index') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            إلغاء
                        </a>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

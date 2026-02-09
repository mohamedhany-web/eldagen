@extends('layouts.app')

@section('title', 'تعديل المادة الدراسية')
@section('header', 'تعديل المادة الدراسية')

@section('content')
<div class="space-y-4 sm:space-y-6 w-full px-3 sm:px-0">
    <!-- معلومات المادة -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">تعديل المادة الدراسية</h3>
                <a href="{{ route('admin.academic-subjects.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors min-h-[44px] sm:min-h-0">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.academic-subjects.update', $academicSubject) }}" class="p-4 sm:p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- السنة الدراسية -->
                <div>
                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        السنة الدراسية <span class="text-red-500">*</span>
                    </label>
                    <select name="academic_year_id" id="academic_year_id" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">اختر السنة الدراسية</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id', $academicSubject->academic_year_id) == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- اسم المادة -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        اسم المادة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $academicSubject->name) }}" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="مثال: الرياضيات">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- رمز المادة -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        رمز المادة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code', $academicSubject->code) }}" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="مثال: MATH">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- اللون -->
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        اللون <span class="text-red-500">*</span>
                    </label>
                    <input type="color" name="color" id="color" value="{{ old('color', $academicSubject->color ?? '#3B82F6') }}" required
                           class="w-full h-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('color')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الأيقونة -->
                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الأيقونة <span class="text-red-500">*</span>
                    </label>
                    <select name="icon" id="icon" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="fas fa-calculator" {{ old('icon', $academicSubject->icon) == 'fas fa-calculator' ? 'selected' : '' }}>🧮 آلة حاسبة (رياضيات)</option>
                        <option value="fas fa-atom" {{ old('icon', $academicSubject->icon) == 'fas fa-atom' ? 'selected' : '' }}>⚛️ ذرة (علوم)</option>
                        <option value="fas fa-book-open" {{ old('icon', $academicSubject->icon) == 'fas fa-book-open' ? 'selected' : '' }}>📖 كتاب مفتوح</option>
                        <option value="fas fa-language" {{ old('icon', $academicSubject->icon) == 'fas fa-language' ? 'selected' : '' }}>🌐 لغات</option>
                        <option value="fas fa-history" {{ old('icon', $academicSubject->icon) == 'fas fa-history' ? 'selected' : '' }}>📜 تاريخ</option>
                        <option value="fas fa-globe" {{ old('icon', $academicSubject->icon) == 'fas fa-globe' ? 'selected' : '' }}>🌍 جغرافيا</option>
                        <option value="fas fa-palette" {{ old('icon', $academicSubject->icon) == 'fas fa-palette' ? 'selected' : '' }}>🎨 فنون</option>
                        <option value="fas fa-music" {{ old('icon', $academicSubject->icon) == 'fas fa-music' ? 'selected' : '' }}>🎵 موسيقى</option>
                        <option value="fas fa-running" {{ old('icon', $academicSubject->icon) == 'fas fa-running' ? 'selected' : '' }}>🏃 رياضة</option>
                        <option value="fas fa-laptop-code" {{ old('icon', $academicSubject->icon) == 'fas fa-laptop-code' ? 'selected' : '' }}>💻 حاسوب</option>
                    </select>
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ترتيب العرض -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        ترتيب العرض
                    </label>
                    <input type="number" name="order" id="order" value="{{ old('order', $academicSubject->order ?? 1) }}" min="1"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="1">
                    @error('order')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- الوصف -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الوصف
                </label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                          placeholder="وصف مختصر للمادة الدراسية (اختياري)">{{ old('description', $academicSubject->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- حالة النشاط -->
            <div class="mt-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           {{ old('is_active', $academicSubject->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                    <label for="is_active" class="mr-2 block text-sm text-gray-700 dark:text-gray-300">
                        المادة نشطة
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    يمكن إضافة كورسات للمواد النشطة فقط
                </p>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.academic-subjects.index') }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        حفظ التعديلات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
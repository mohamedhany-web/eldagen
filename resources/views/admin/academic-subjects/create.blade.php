@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4 sm:py-8">
    <div class="w-full px-3 sm:px-6 lg:px-8">
        <!-- الهيدر -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg mb-4 sm:mb-6">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">إضافة مادة دراسية</h1>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">إنشاء مادة دراسية جديدة (مثل: الرياضيات، الفيزياء...)</p>
                    </div>
                    <a href="{{ route('admin.academic-subjects.index') }}"
                       class="inline-flex items-center justify-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg font-medium transition-colors min-h-[44px]">
                        <i class="fas fa-arrow-right"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <!-- نموذج الإضافة -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg">
            <form method="POST" action="{{ route('admin.academic-subjects.store') }}">
                @csrf
                <div class="px-4 sm:px-6 py-4 sm:py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- اسم المادة -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                اسم المادة <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="مثال: الرياضيات">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- السنة الدراسية -->
                        <div>
                            <label for="academic_year_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                السنة الدراسية <span class="text-red-500">*</span>
                            </label>
                            <select name="academic_year_id" id="academic_year_id" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">-- اختر السنة الدراسية --</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- رمز المادة -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                رمز المادة <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="مثال: MATH, PHYS">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">رمز مختصر بالإنجليزية (فريد ضمن نفس السنة)</p>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ترتيب العرض -->
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                ترتيب العرض
                            </label>
                            <input type="number" name="order" id="order" value="{{ old('order', 1) }}" min="1"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="1">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                ترتيب ظهور المادة في القائمة
                            </p>
                            @error('order')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- اللون والأيقونة -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                اللون <span class="text-red-500">*</span>
                            </label>
                            <input type="color" name="color" id="color" value="{{ old('color', '#3B82F6') }}"
                                   class="w-full h-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                            @error('color')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                الأيقونة <span class="text-red-500">*</span>
                            </label>
                            <select name="icon" id="icon" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="fas fa-calculator" {{ old('icon') == 'fas fa-calculator' ? 'selected' : '' }}>آلة حاسبة (رياضيات)</option>
                                <option value="fas fa-atom" {{ old('icon') == 'fas fa-atom' ? 'selected' : '' }}>ذرة (فيزياء/كيمياء)</option>
                                <option value="fas fa-dna" {{ old('icon') == 'fas fa-dna' ? 'selected' : '' }}>أحياء</option>
                                <option value="fas fa-book" {{ old('icon', 'fas fa-book') == 'fas fa-book' ? 'selected' : '' }}>كتاب</option>
                                <option value="fas fa-language" {{ old('icon') == 'fas fa-language' ? 'selected' : '' }}>لغة</option>
                                <option value="fas fa-globe" {{ old('icon') == 'fas fa-globe' ? 'selected' : '' }}>جغرافيا</option>
                                <option value="fas fa-landmark" {{ old('icon') == 'fas fa-landmark' ? 'selected' : '' }}>تاريخ</option>
                                <option value="fas fa-graduation-cap" {{ old('icon') == 'fas fa-graduation-cap' ? 'selected' : '' }}>تخرج</option>
                            </select>
                            @error('icon')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- الوصف -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                الوصف
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                      placeholder="وصف مختصر للمادة الدراسية (اختياري)">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- حالة المادة -->
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                                <label for="is_active" class="mr-2 block text-sm text-gray-700 dark:text-gray-300">
                                    المادة نشطة
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                يمكن إضافة كورسات للمواد النشطة فقط
                            </p>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="px-4 sm:px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 rounded-b-lg">
                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('admin.academic-subjects.index') }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            إلغاء
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            حفظ المادة
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- أمثلة المواد الدراسية -->
        <div class="mt-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <h3 class="text-sm font-medium text-green-800 dark:text-green-200 mb-2">أمثلة على المواد الدراسية:</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-green-700 dark:text-green-300">
                <span>• الرياضيات</span>
                <span>• الفيزياء</span>
                <span>• الكيمياء</span>
                <span>• الأحياء</span>
                <span>• اللغة العربية</span>
                <span>• اللغة الإنجليزية</span>
                <span>• التاريخ</span>
                <span>• الجغرافيا</span>
            </div>
        </div>
    </div>
</div>
@endsection
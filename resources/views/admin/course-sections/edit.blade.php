@extends('layouts.app')

@section('title', 'تعديل القسم')
@section('header', 'تعديل القسم: ' . $section->title)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <nav class="text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
            <span class="mx-2">/</span>
            <a href="{{ route('admin.courses.sections.index', $course) }}" class="hover:text-primary-600">أقسام {{ $course->title }}</a>
            <span class="mx-2">/</span>
            <span>تعديل</span>
        </nav>
        <a href="{{ route('admin.courses.sections.index', $course) }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">تعديل بيانات القسم</h4>
        </div>
        <form action="{{ route('admin.courses.sections.update', [$course, $section]) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">عنوان القسم <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $section->title) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الترتيب</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $section->order) }}" min="0"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                </div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $section->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                    <span class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">قسم نشط</span>
                </label>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <a href="{{ route('admin.courses.sections.index', $course) }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-medium">إلغاء</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium">
                    <i class="fas fa-save ml-2"></i>
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

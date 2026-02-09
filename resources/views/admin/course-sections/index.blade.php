@extends('layouts.app')

@section('title', 'أقسام الكورس')
@section('header', 'أقسام الكورس: ' . $course->title)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.advanced-courses.index') }}" class="hover:text-primary-600">الكورسات</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.advanced-courses.show', $course) }}" class="hover:text-primary-600">{{ $course->title }}</a>
                <span class="mx-2">/</span>
                <span>الأقسام</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.courses.sections.create', $course) }}"
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus ml-2"></i>
                إضافة قسم
            </a>
            <a href="{{ route('admin.courses.lessons.index', $course) }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-list ml-2"></i>
                الدروس
            </a>
            <a href="{{ route('admin.advanced-courses.show', $course) }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">تنظيم المحتوى بأقسام (مثلاً: محاضرات، واجبات)</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">أنشئ أقساماً ثم عيّن كل درس إلى قسم من صفحة الدروس.</p>

        @if($sections->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700" id="sections-container">
                @foreach($sections as $section)
                    <div class="py-4 flex items-center justify-between" data-section-id="{{ $section->id }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-grip-vertical text-gray-400 cursor-move"></i>
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                <i class="fas fa-folder text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $section->title }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $section->lessons_count }} درس</p>
                            </div>
                            @if(!$section->is_active)
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300">غير نشط</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.courses.sections.edit', [$course, $section]) }}"
                               class="p-2 text-indigo-600 hover:text-indigo-800 transition-colors" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.courses.sections.destroy', [$course, $section]) }}"
                                  class="inline" onsubmit="return confirm('حذف القسم؟ الدروس ستبقى بدون قسم.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:text-red-800 transition-colors" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-folder-open text-2xl text-gray-400"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">لا توجد أقسام</h4>
                <p class="text-gray-500 dark:text-gray-400 mb-4">أضف أقساماً مثل "محاضرات" و "تمارين" ثم ربط الدروس بها من صفحة الدروس.</p>
                <a href="{{ route('admin.courses.sections.create', $course) }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة قسم
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

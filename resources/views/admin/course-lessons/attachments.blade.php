@extends('layouts.app')

@section('title', 'مرفقات الدرس: ' . $lesson->title)
@section('header', 'مرفقات الدرس: ' . $lesson->title)

@section('content')
<div class="space-y-6">
    <!-- الهيدر والعودة -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.advanced-courses.index') }}" class="hover:text-primary-600">الكورسات</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.courses.lessons.index', $course) }}" class="hover:text-primary-600">دروس {{ $course->title }}</a>
                <span class="mx-2">/</span>
                <span>مرفقات: {{ $lesson->title }}</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.courses.lessons.edit', [$course, $lesson]) }}" 
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-edit ml-2"></i>
                تعديل الدرس
            </a>
            <a href="{{ route('admin.courses.lessons.show', [$course, $lesson]) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-eye ml-2"></i>
                عرض الدرس
            </a>
            <a href="{{ route('admin.courses.lessons.index', $course) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للدروس
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- المرفقات -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-paperclip ml-2"></i>
                مرفقات الدرس
            </h4>
        </div>
        <div class="p-6">
            @php
                $attachments = $lesson->getAttachmentsArray();
            @endphp
            @if($attachments && count($attachments) > 0)
                <div class="space-y-3">
                    @foreach($attachments as $attachment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file text-primary-600 dark:text-primary-400"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $attachment['name'] ?? 'ملف' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format(($attachment['size'] ?? 0) / 1024, 2) }} KB</div>
                                </div>
                            </div>
                            <a href="{{ storage_url($attachment['path'] ?? '') }}" 
                               target="_blank"
                               class="text-primary-600 hover:text-primary-700 font-medium">
                                <i class="fas fa-download ml-1"></i>
                                تحميل
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">
                    <i class="fas fa-inbox text-4xl mb-3 block"></i>
                    لا توجد مرفقات لهذا الدرس. يمكنك إضافة مرفقات من صفحة تعديل الدرس.
                </p>
            @endif
        </div>
    </div>
</div>
@endsection

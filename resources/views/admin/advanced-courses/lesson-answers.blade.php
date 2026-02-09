@extends('layouts.app')

@section('title', 'إجابات الطلاب - الدروس')
@section('header', 'إجابات الطلاب على أسئلة الدروس: ' . $course->title)

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
                <a href="{{ route('admin.advanced-courses.show', $course) }}" class="hover:text-primary-600">{{ $course->title }}</a>
                <span class="mx-2">/</span>
                <span>إجابات الطلاب في الدروس</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.advanced-courses.show', $course) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للكورس
            </a>
            <a href="{{ route('admin.courses.lessons.index', $course) }}" 
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-play-circle ml-2"></i>
                الدروس
            </a>
        </div>
    </div>

    <!-- وصف الصفحة -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-gray-600 dark:text-gray-400 text-sm">
            <i class="fas fa-info-circle ml-2"></i>
            هذه الصفحة تعرض جميع الدروس في الكورس، وفي كل درس تُعرض أسئلة الفيديو (نقاط التوقف) مع إجابات جميع الطلاب على كل سؤال.
        </p>
    </div>

    <!-- قائمة الدروس -->
    @forelse($lessons as $lesson)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- عنوان الدرس -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400">
                        <i class="fas fa-video"></i>
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $lesson->title }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $lesson->type === 'video' ? 'فيديو' : $lesson->type }} —
                            @if($lesson->videoQuestions->count() > 0)
                                {{ $lesson->videoQuestions->count() }} سؤال في الفيديو
                            @else
                                لا توجد أسئلة في الفيديو
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.courses.lessons.edit', [$course, $lesson]) }}" 
                   class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    <i class="fas fa-edit ml-1"></i>
                    تعديل الدرس
                </a>
            </div>

            <!-- أسئلة الفيديو وإجابات الطلاب -->
            @if($lesson->videoQuestions->count() > 0)
                <div class="p-6 space-y-6">
                    @foreach($lesson->videoQuestions as $vq)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                            <div class="px-4 py-3 bg-gray-100 dark:bg-gray-700 flex items-center justify-between">
                                <div>
                                    <span class="font-mono text-sm text-primary-600 dark:text-primary-400">{{ $vq->time_formatted }}</span>
                                    <span class="text-gray-500 dark:text-gray-400 mx-2">—</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ Str::limit($vq->question->question ?? 'سؤال', 80) }}</span>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $vq->answers->count() }} إجابة</span>
                            </div>
                            <div class="overflow-x-auto">
                                @if($vq->answers->count() > 0)
                                    <table class="w-full text-right text-sm">
                                        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                            <tr>
                                                <th class="px-4 py-2 font-medium">الطالب</th>
                                                <th class="px-4 py-2 font-medium">الإجابة</th>
                                                <th class="px-4 py-2 font-medium">النتيجة</th>
                                                <th class="px-4 py-2 font-medium">التاريخ</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                            @foreach($vq->answers as $answer)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                    <td class="px-4 py-3 text-gray-900 dark:text-white">
                                                        {{ $answer->user->name ?? '—' }}
                                                        @if($answer->user->email ?? null)
                                                            <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $answer->user->email }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200 max-w-xs truncate" title="{{ $answer->answer }}">
                                                        {{ Str::limit($answer->answer, 50) }}
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if($answer->is_correct)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                <i class="fas fa-check ml-1"></i>
                                                                صحيحة
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                                <i class="fas fa-times ml-1"></i>
                                                                خاطئة
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                                        {{ $answer->created_at->format('Y-m-d H:i') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="px-4 py-6 text-center text-gray-500 dark:text-gray-400 text-sm">لا توجد إجابات من الطلاب على هذا السؤال بعد.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-question-circle text-3xl mb-2"></i>
                    <p>لا توجد أسئلة في الفيديو لهذا الدرس. يمكنك إضافة أسئلة من صفحة تعديل الدرس.</p>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center border border-gray-200 dark:border-gray-700">
            <i class="fas fa-book-open text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600 dark:text-gray-400">لا توجد دروس في هذا الكورس بعد.</p>
            <a href="{{ route('admin.courses.lessons.create', $course) }}" 
               class="inline-flex items-center mt-4 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium">
                <i class="fas fa-plus ml-2"></i>
                إضافة درس
            </a>
        </div>
    @endforelse
</div>
@endsection

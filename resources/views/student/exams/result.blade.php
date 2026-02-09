@extends('layouts.app')

@section('content')
<div class="w-full max-w-full min-w-0 p-3 sm:p-6 -mx-4 sm:mx-0 px-3 sm:px-0">
    <div class="mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ __('نتيجة الامتحان') }}</h1>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 truncate">{{ $exam->title }}</p>
            </div>
            <a href="{{ route('student.exams.index') }}" 
               class="inline-flex items-center justify-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2.5 rounded-xl font-medium transition-colors w-full sm:w-auto flex-shrink-0">
                <i class="fas fa-arrow-right"></i>
                {{ __('العودة للامتحانات') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- النتيجة الإجمالية -->
        <div class="lg:col-span-1 min-w-0">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="text-center">
                    <div class="w-32 h-32 mx-auto mb-4 relative">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-300 dark:text-gray-600" stroke="currentColor" stroke-width="3" fill="none"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            <path class="text-{{ $attempt->result_color }}-500" stroke="currentColor" stroke-width="3" fill="none" 
                                  stroke-linecap="round" stroke-dasharray="{{ $attempt->percentage }}, 100"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($attempt->percentage, 1) }}%
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $attempt->result_status }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('النقاط المحصل عليها') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $attempt->score ?? 0 }} / {{ $exam->total_marks ?? $exam->calculateTotalMarks() }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('الوقت المستغرق') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $attempt->formatted_time ?? 'غير محدد' }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('تاريخ التسليم') }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i') : '-' }}
                            </span>
                        </div>

                        @if($attempt->auto_submitted)
                            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                                <div class="flex items-center text-amber-700 dark:text-amber-300">
                                    <i class="fas fa-clock ml-2"></i>
                                    <span class="text-sm">{{ __('تم التسليم تلقائياً بانتهاء الوقت') }}</span>
                                </div>
                            </div>
                        @endif

                        @if($attempt->tab_switches > 0)
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <div class="flex items-center text-red-700 dark:text-red-300">
                                    <i class="fas fa-exclamation-triangle ml-2"></i>
                                    <span class="text-sm">
                                        {{ __('تبديل التبويبات') }}: {{ $attempt->tab_switches }} {{ __('مرة') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($attempt->feedback)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                        <i class="fas fa-comment ml-2"></i>
                        {{ __('تعليقات المصحح') }}
                    </h3>
                    <div class="text-gray-700 dark:text-gray-300 leading-relaxed">
                        {!! nl2br(e($attempt->feedback)) !!}
                    </div>
                    @if($attempt->reviewed_by)
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('تم التصحيح بواسطة') }}: {{ $attempt->reviewer->name ?? 'المصحح' }}
                            @if($attempt->reviewed_at)
                                {{ __('في') }} {{ $attempt->reviewed_at->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- تفاصيل الأسئلة والإجابات -->
        <div class="lg:col-span-2 min-w-0">
            @if($exam->show_correct_answers || $exam->allow_review)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('مراجعة الأسئلة والإجابات') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                            {{ __('يمكنك مراجعة إجاباتك والإجابات الصحيحة') }}
                        </p>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($exam->examQuestions as $index => $examQuestion)
                            @php
                                $question = $examQuestion->question;
                                $userAnswer = $attempt->answers[$question->id] ?? null;
                                $isCorrect = $question->isCorrectAnswer($userAnswer);
                            @endphp
                            
                            <div class="p-4 sm:p-6">
                                <!-- رأس السؤال -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center">
                                        <span class="bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 text-sm font-medium px-3 py-1 rounded-full ml-3">
                                            {{ __('السؤال') }} {{ $index + 1 }}
                                        </span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            ({{ $examQuestion->marks }} {{ __('نقطة') }})
                                        </span>
                                    </div>
                                    
                                    @if($isCorrect !== null)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $isCorrect ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                            @if($isCorrect)
                                                <i class="fas fa-check ml-1"></i>
                                                {{ __('صحيح') }}
                                            @else
                                                <i class="fas fa-times ml-1"></i>
                                                {{ __('خطأ') }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            <i class="fas fa-question ml-1"></i>
                                            {{ __('يحتاج مراجعة') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- نص السؤال (لا يظهر لسؤال بصورة عندما النص هو "سؤال بصورة") -->
                                @if($question->type !== 'image_multiple_choice')
                                    <div class="mb-4">
                                        <div class="text-gray-900 dark:text-white leading-relaxed">
                                            {!! nl2br(e($question->question)) !!}
                                        </div>
                                    </div>
                                @endif

                                <!-- صورة السؤال -->
                                @if($question->image_url)
                                    <div class="mb-4">
                                        <img src="{{ $question->getImageUrl() }}" 
                                             alt="صورة السؤال" 
                                             class="max-w-full h-auto rounded-lg border border-gray-200 dark:border-gray-600">
                                    </div>
                                @endif

                                <!-- الإجابات -->
                                @if(($question->type === 'multiple_choice' || $question->type === 'image_multiple_choice') && $question->options)
                                    <div class="space-y-2">
                                        @foreach($question->options as $optionIndex => $option)
                                            @php
                                                $optionLetter = chr(65 + $optionIndex);
                                                $userAns = trim((string)($userAnswer ?? ''));
                                                $optText = trim((string)$option);
                                                $isUserAnswer = $userAns === $optText;
                                                $correctArr = array_map(function ($c) { return trim((string)$c); }, (array)($question->correct_answer ?? []));
                                                $isCorrectAnswer = in_array($optText, $correctArr);
                                                if (!$isCorrectAnswer && count($correctArr) === 1 && is_numeric($correctArr[0])) {
                                                    $idx = (int)$correctArr[0];
                                                    $isCorrectAnswer = isset($question->options[$idx]) && trim((string)$question->options[$idx]) === $optText;
                                                }
                                            @endphp
                                            
                                            <div class="flex items-center p-3 rounded-lg border
                                                @if($exam->show_correct_answers && $isCorrectAnswer)
                                                    border-green-300 bg-green-50 dark:border-green-700 dark:bg-green-900/20
                                                @elseif($isUserAnswer && !$isCorrectAnswer)
                                                    border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-900/20
                                                @elseif($isUserAnswer)
                                                    border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/20
                                                @else
                                                    border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-700
                                                @endif">
                                                
                                                <div class="flex items-center">
                                                    <div class="w-6 h-6 border-2 rounded-full flex items-center justify-center ml-3
                                                        @if($exam->show_correct_answers && $isCorrectAnswer)
                                                            border-green-500 bg-green-500 text-white
                                                        @elseif($isUserAnswer && !$isCorrectAnswer)
                                                            border-red-500 bg-red-500 text-white
                                                        @elseif($isUserAnswer)
                                                            border-blue-500 bg-blue-500 text-white
                                                        @else
                                                            border-gray-400 dark:border-gray-500
                                                        @endif">
                                                        <span class="text-sm font-medium">{{ $optionLetter }}</span>
                                                    </div>
                                                    <span class="text-gray-900 dark:text-white">{{ $option }}</span>
                                                </div>
                                                
                                                <div class="mr-auto flex space-x-1 space-x-reverse">
                                                    @if($isUserAnswer)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                            {{ __('إجابتك') }}
                                                        </span>
                                                    @endif
                                                    @if($exam->show_correct_answers && $isCorrectAnswer)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                            {{ __('الإجابة الصحيحة') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($question->type === 'true_false')
                                    @php
                                        $tfCorrect = is_array($question->correct_answer) ? ($question->correct_answer[0] ?? '') : ($question->correct_answer ?? '');
                                        $tfCorrect = ($tfCorrect === 'صحيح') ? 'صح' : trim((string)$tfCorrect);
                                    @endphp
                                    <div class="space-y-2">
                                        @foreach(['صح' => 'صح', 'خطأ' => 'خطأ'] as $value => $label)
                                            @php
                                                $isUserAnswer = trim((string)($userAnswer ?? '')) === $value;
                                                $isCorrectAnswer = $tfCorrect === $value;
                                            @endphp
                                            
                                            <div class="flex items-center p-3 rounded-lg border
                                                @if($exam->show_correct_answers && $isCorrectAnswer)
                                                    border-green-300 bg-green-50 dark:border-green-700 dark:bg-green-900/20
                                                @elseif($isUserAnswer && !$isCorrectAnswer)
                                                    border-red-300 bg-red-50 dark:border-red-700 dark:bg-red-900/20
                                                @elseif($isUserAnswer)
                                                    border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/20
                                                @else
                                                    border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-700
                                                @endif">
                                                
                                                <span class="text-gray-900 dark:text-white">{{ $label }}</span>
                                                
                                                <div class="mr-auto flex space-x-1 space-x-reverse">
                                                    @if($isUserAnswer)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                            {{ __('إجابتك') }}
                                                        </span>
                                                    @endif
                                                    @if($exam->show_correct_answers && $isCorrectAnswer)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                            {{ __('الإجابة الصحيحة') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif(in_array($question->type, ['fill_blank', 'short_answer']))
                                    <div class="space-y-3">
                                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('إجابتك') }}:</div>
                                            <div class="text-gray-900 dark:text-white">
                                                {{ $userAnswer ?: __('لم تتم الإجابة') }}
                                            </div>
                                        </div>
                                        
                                        @if($exam->show_correct_answers && $question->correct_answer)
                                            <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                                <div class="text-sm text-green-700 dark:text-green-300 mb-1">{{ __('الإجابة الصحيحة') }}:</div>
                                                <div class="text-green-900 dark:text-green-100">
                                                    @if(is_array($question->correct_answer))
                                                        {{ implode(' أو ', $question->correct_answer) }}
                                                    @else
                                                        {{ $question->correct_answer }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($question->type === 'essay')
                                    <div class="space-y-3">
                                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('إجابتك') }}:</div>
                                            <div class="text-gray-900 dark:text-white leading-relaxed">
                                                @if($userAnswer)
                                                    {!! nl2br(e($userAnswer)) !!}
                                                @else
                                                    <em class="text-gray-500">{{ __('لم تتم الإجابة') }}</em>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- شرح الإجابة -->
                                @if($exam->show_explanations && $question->explanation)
                                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                        <div class="flex items-start">
                                            <i class="fas fa-info-circle text-blue-500 ml-2 mt-0.5"></i>
                                            <div>
                                                <div class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-1">
                                                    {{ __('شرح الإجابة') }}:
                                                </div>
                                                <div class="text-blue-800 dark:text-blue-200 text-sm leading-relaxed">
                                                    {!! nl2br(e($question->explanation)) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center">
                    <div class="text-gray-400 text-6xl mb-4">
                        <i class="fas fa-eye-slash"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        {{ __('مراجعة الأسئلة غير متاحة') }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('لا يسمح بمراجعة الأسئلة والإجابات لهذا الامتحان') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.result-content {
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    line-height: 1.8;
}
</style>
@endpush

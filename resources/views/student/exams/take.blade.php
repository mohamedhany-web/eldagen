@extends('layouts.app')

@section('title', $exam->title)
@section('header', '')

@section('content')
<div class="w-full max-w-full min-w-0 min-h-screen bg-gray-950 text-white flex flex-col overflow-hidden" x-data="{ questionListOpen: false }">
    <!-- حاوية الامتحان — التمرير بداخلها فقط، والشريط ثابت داخل الحاوية -->
    <div class="flex-1 flex flex-col min-h-0 overflow-y-auto w-full">
        <!-- شريط التحكم — ثابت داخل حاوية الامتحان (sticky داخل الـ container) -->
        <header class="sticky top-0 z-30 shrink-0 bg-gray-900 border-b border-gray-700 shadow-xl w-full">
            <div class="w-full px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- قسم اليمين: معلومات الامتحان -->
                <div class="flex items-center gap-3 min-w-0 order-2 sm:order-1 w-full sm:w-auto justify-center sm:justify-start">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                        <i class="fas fa-clipboard-check text-white"></i>
                    </div>
                    <div class="min-w-0 text-center sm:text-right">
                        <h1 class="text-sm sm:text-base font-bold text-white truncate">{{ $exam->title }}</h1>
                        <p class="text-xs text-gray-400 truncate hidden sm:block">{{ $exam->course->title }}</p>
                    </div>
                </div>

                <!-- قسم الوسط: التايمر — بارز ومركّز -->
                <div class="order-1 sm:order-2 flex flex-col items-center justify-center px-6 py-3 bg-gray-800 rounded-2xl border-2 border-amber-500/50 shadow-lg min-w-[140px] sm:min-w-[180px]">
                    <span class="text-xs font-medium text-amber-400 uppercase tracking-wider mb-1">الوقت المتبقي</span>
                    <div id="timer" class="text-2xl sm:text-3xl font-black text-amber-400 tabular-nums tracking-wider">{{ sprintf('%02d:%02d', floor($attempt->remaining_time / 60), $attempt->remaining_time % 60) }}</div>
                    <span class="text-xs text-gray-500 mt-0.5">دقيقة : ثانية</span>
                </div>

                <!-- قسم اليسار: التقدم + قائمة الأسئلة + التسليم -->
                <div class="flex items-center gap-2 sm:gap-4 order-3 w-full sm:w-auto justify-center sm:justify-end flex-wrap">
                    <div class="flex items-center gap-2 px-4 py-2 bg-gray-800 rounded-xl border border-gray-600">
                        <i class="fas fa-list-check text-indigo-400 text-sm"></i>
                        <span id="progress-text" class="text-sm font-bold text-white tabular-nums">0 / {{ $questions->count() }}</span>
                        <span class="text-xs text-gray-400">مجاب</span>
                    </div>
                    <button type="button" @click="questionListOpen = true" 
                            class="lg:hidden flex items-center gap-2 bg-gray-700 hover:bg-gray-600 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-gray-600">
                        <i class="fas fa-list-ol"></i>
                        <span class="text-sm">الأسئلة</span>
                    </button>
                    <button onclick="confirmSubmit()" 
                            class="flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white px-5 py-2.5 rounded-xl font-bold transition-colors shadow-lg border border-green-500/50">
                        <i class="fas fa-check"></i>
                        <span class="text-sm sm:text-base">تسليم الامتحان</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- شريط تقدم الامتحان (مرئي) -->
        <div class="w-full max-w-4xl mx-auto px-4 pb-2 hidden sm:block">
            <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                <span>تقدم الامتحان</span>
                <span id="header-progress-text">1 / {{ $questions->count() }}</span>
            </div>
            <div class="h-1.5 bg-gray-700 rounded-full overflow-hidden">
                <div id="header-progress-bar" class="h-full bg-indigo-500 rounded-full transition-all duration-300" style="width: {{ $questions->count() ? (100 / $questions->count()) : 0 }}%;"></div>
            </div>
        </div>
    </header>

    <!-- محتوى الامتحان — عرض كامل، التمرير للأسئلة فقط -->
    <div class="flex flex-col lg:flex-row flex-1 min-h-0 w-full">
        <!-- قائمة الأسئلة الجانبية - مخفية على الموبايل وتظهر في درج -->
        <div class="hidden lg:block lg:w-64 xl:w-72 bg-gray-800 border-l border-gray-700 overflow-y-auto flex-shrink-0">
            <div class="p-4 border-b border-gray-700">
                <h3 class="font-medium text-gray-200">قائمة الأسئلة</h3>
            </div>
            <div class="p-2 max-h-[calc(100vh-8rem)] overflow-y-auto">
                @foreach($questions as $index => $examQuestion)
                    <button onclick="goToQuestion({{ $index }})" 
                            id="question-nav-{{ $index }}"
                            class="w-full text-right p-3 mb-2 rounded-lg transition-colors question-nav-btn
                                   {{ $index == 0 ? 'bg-indigo-600 text-white' : 'bg-gray-700 hover:bg-gray-600 text-gray-300' }}">
                        <div class="flex items-center justify-between">
                            <span class="text-sm">السؤال {{ $index + 1 }}</span>
                            <div class="w-4 h-4 rounded-full border-2 border-gray-400 flex-shrink-0" id="question-status-{{ $index }}"></div>
                        </div>
                        <div class="text-xs text-gray-400 mt-1">{{ $examQuestion->marks }} نقطة</div>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- درج قائمة الأسئلة للموبايل -->
        <div x-show="questionListOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 lg:hidden" style="display: none;">
            <div class="absolute inset-0 bg-black/70" @click="questionListOpen = false"></div>
            <div class="absolute bottom-0 left-0 right-0 top-1/4 bg-gray-800 rounded-t-2xl border-t border-gray-700 overflow-hidden flex flex-col max-h-[75vh]">
                <div class="p-4 border-b border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-200">قائمة الأسئلة</h3>
                    <button type="button" @click="questionListOpen = false" class="p-2 rounded-lg hover:bg-gray-700 text-gray-400">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-2 overflow-y-auto flex-1">
                    @foreach($questions as $index => $examQuestion)
                        <button onclick="goToQuestion({{ $index }}); questionListOpen = false;" 
                                id="question-nav-mobile-{{ $index }}"
                                class="w-full text-right p-3 mb-2 rounded-lg transition-colors
                                       {{ $index == 0 ? 'bg-indigo-600 text-white' : 'bg-gray-700 hover:bg-gray-600 text-gray-300' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">السؤال {{ $index + 1 }}</span>
                                <span class="text-xs text-gray-400">{{ $examQuestion->marks }} نقطة</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- منطقة الأسئلة — عرض كامل، محتوى مركّز -->
        <div class="flex-1 overflow-y-auto min-w-0 w-full flex flex-col items-center py-6 sm:py-8 px-2 sm:px-4 lg:px-6">
            <div class="w-full max-w-4xl mx-auto flex flex-col items-center">
                @foreach($questions as $index => $examQuestion)
                    <div class="question-container w-full {{ $index == 0 ? '' : 'hidden' }}" id="question-{{ $index }}">
                        <div class="bg-gray-800/95 rounded-2xl p-5 sm:p-8 border border-gray-600 shadow-2xl shadow-black/30 w-full max-w-4xl mx-auto">
                            <!-- رأس السؤال -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 sm:mb-6">
                                <div class="min-w-0">
                                    <h2 class="text-lg sm:text-xl font-bold text-white">السؤال {{ $index + 1 }}</h2>
                                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs sm:text-sm text-gray-400 mt-1">
                                        <span>{{ $examQuestion->marks }} نقطة</span>
                                        <span>{{ $examQuestion->question->type_text }}</span>
                                        @if($examQuestion->question->difficulty_level)
                                            <span class="px-2 py-0.5 rounded text-xs
                                                @if($examQuestion->question->difficulty_level == 'easy') bg-green-900 text-green-300
                                                @elseif($examQuestion->question->difficulty_level == 'medium') bg-yellow-900 text-yellow-300
                                                @else bg-red-900 text-red-300
                                                @endif">
                                                {{ $examQuestion->question->difficulty_text }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if($examQuestion->time_limit)
                                    <div class="text-center flex-shrink-0 px-3 py-1.5 bg-gray-700 rounded-lg">
                                        <div class="text-base sm:text-lg font-bold text-yellow-400 tabular-nums" id="question-timer-{{ $index }}">{{ gmdate('i:s', $examQuestion->time_limit) }}</div>
                                        <div class="text-xs text-gray-400">وقت السؤال</div>
                                    </div>
                                @endif
                            </div>

                            <!-- نص السؤال -->
                            <div class="mb-4 sm:mb-6">
                                @if($examQuestion->question->type !== 'image_multiple_choice')
                                    <div class="text-base sm:text-lg text-white leading-relaxed break-words">{{ $examQuestion->question->question }}</div>
                                @endif
                                
                                <!-- صورة السؤال -->
                                @if($examQuestion->question->image_url)
                                    <div class="mt-3 sm:mt-4">
                                        <img src="{{ $examQuestion->question->secure_image_url }}" 
                                             alt="صورة السؤال" 
                                             class="max-w-full h-auto rounded-lg border border-gray-600 w-full object-contain"
                                             style="max-height: min(280px, 50vh);">
                                    </div>
                                @endif

                                @if($examQuestion->question->audio_url)
                                    <div class="mt-4">
                                        <audio controls class="w-full">
                                            <source src="{{ $examQuestion->question->audio_url }}" type="audio/mpeg">
                                            متصفحك لا يدعم تشغيل الصوت.
                                        </audio>
                                    </div>
                                @endif

                                @if($examQuestion->question->video_url)
                                    <div class="mt-4">
                                        <div class="bg-black rounded-lg overflow-hidden" style="aspect-ratio: 16/9;">
                                            {!! \App\Helpers\VideoHelper::generateEmbedHtml($examQuestion->question->video_url, '100%', '100%') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- خيارات الإجابة -->
                            <div class="space-y-2 sm:space-y-3" id="answer-options-{{ $index }}">
                                @if($examQuestion->question->type == 'multiple_choice' || $examQuestion->question->type == 'image_multiple_choice')
                                    @foreach($exam->randomize_options ? $examQuestion->question->shuffled_options : $examQuestion->question->options as $optionIndex => $option)
                                        <label class="flex items-start sm:items-center gap-3 p-3 sm:p-4 bg-gray-700 hover:bg-gray-600 rounded-lg cursor-pointer transition-colors">
                                            <input type="radio" 
                                                   name="answer_{{ $examQuestion->question->id }}" 
                                                   value="{{ $option }}"
                                                   class="w-5 h-5 mt-0.5 sm:mt-0 flex-shrink-0 text-indigo-600 bg-gray-600 border-gray-500 focus:ring-indigo-500"
                                                   onchange="saveAnswer({{ $examQuestion->question->id }}, this.value)">
                                            <span class="mr-0 sm:mr-3 text-white text-sm sm:text-base break-words flex-1">{{ $option }}</span>
                                        </label>
                                    @endforeach

                                @elseif($examQuestion->question->type == 'true_false')
                                    <div class="grid grid-cols-2 gap-2 sm:gap-3">
                                        <label class="flex items-center justify-center p-4 bg-gray-700 hover:bg-gray-600 rounded-lg cursor-pointer transition-colors">
                                            <input type="radio" 
                                                   name="answer_{{ $examQuestion->question->id }}" 
                                                   value="صح"
                                                   class="w-5 h-5 text-indigo-600 bg-gray-600 border-gray-500 focus:ring-indigo-500 ml-2"
                                                   onchange="saveAnswer({{ $examQuestion->question->id }}, 'صح')">
                                            <span class="text-white font-medium">صح</span>
                                        </label>
                                        <label class="flex items-center justify-center p-4 bg-gray-700 hover:bg-gray-600 rounded-lg cursor-pointer transition-colors">
                                            <input type="radio" 
                                                   name="answer_{{ $examQuestion->question->id }}" 
                                                   value="خطأ"
                                                   class="w-5 h-5 text-indigo-600 bg-gray-600 border-gray-500 focus:ring-indigo-500 ml-2"
                                                   onchange="saveAnswer({{ $examQuestion->question->id }}, 'خطأ')">
                                            <span class="text-white font-medium">خطأ</span>
                                        </label>
                                    </div>

                                @elseif($examQuestion->question->type == 'fill_blank')
                                    <input type="text" 
                                           id="answer_{{ $examQuestion->question->id }}"
                                           placeholder="اكتب إجابتك هنا..."
                                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-base"
                                           onchange="saveAnswer({{ $examQuestion->question->id }}, this.value)">

                                @elseif($examQuestion->question->type == 'short_answer' || $examQuestion->question->type == 'essay')
                                    <textarea id="answer_{{ $examQuestion->question->id }}"
                                              rows="{{ $examQuestion->question->type == 'essay' ? 5 : 3 }}"
                                              placeholder="اكتب إجابتك هنا..."
                                              class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-base min-h-[100px]"
                                              onchange="saveAnswer({{ $examQuestion->question->id }}, this.value)"></textarea>
                                @endif
                            </div>

                            <!-- أزرار التنقل -->
                            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-4 mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-700">
                                <button onclick="previousQuestion()" 
                                        id="prev-btn"
                                        class="order-2 sm:order-1 px-6 py-3 bg-gray-600 hover:bg-gray-500 text-white rounded-xl font-semibold transition-colors {{ $index == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $index == 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-arrow-right ml-2"></i>
                                    السابق
                                </button>

                                <div class="text-center order-1 sm:order-2">
                                    <span class="text-sm font-medium text-gray-400 block mb-2">السؤال {{ $index + 1 }} من {{ $questions->count() }}</span>
                                    <div class="w-full max-w-xs mx-auto bg-gray-700 rounded-full h-2 overflow-hidden">
                                        <div class="bg-indigo-500 h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ (($index + 1) / $questions->count()) * 100 }}%"></div>
                                    </div>
                                </div>

                                <button onclick="nextQuestion()" 
                                        id="next-btn"
                                        class="order-3 px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold transition-colors shadow-lg">
                                    التالي
                                    <i class="fas fa-arrow-left mr-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    </div>
    <!-- نهاية حاوية الامتحان (التمرير) -->

    <!-- نافذة تأكيد التسليم -->
    <div id="submitModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-90 overflow-y-auto h-full w-full z-50 flex items-start sm:items-center justify-center p-4">
        <div class="relative w-full max-w-md mx-auto p-4 sm:p-5 border shadow-xl rounded-2xl bg-gray-800 border-gray-600">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-900">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-white mt-4">تأكيد تسليم الامتحان</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-300">
                        هل أنت متأكد من تسليم الامتحان؟ لن تتمكن من تعديل إجاباتك بعد التسليم.
                    </p>
                    <div class="mt-4 p-3 bg-blue-900 rounded border border-blue-700">
                        <div class="text-sm text-blue-200">
                            <div>الأسئلة المجابة: <span id="answered-count">0</span> من {{ $questions->count() }}</div>
                            <div>الوقت المتبقي: <span id="submit-timer">--:--</span></div>
                        </div>
                    </div>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="submitExam()" 
                            class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-green-700 transition-colors">
                        تسليم
                    </button>
                    <button onclick="closeSubmitModal()" 
                            class="px-4 py-2 bg-gray-600 text-white text-base font-medium rounded-md w-24 hover:bg-gray-500 transition-colors">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- تحذير تبديل التبويب -->
    <div id="tabSwitchWarning" class="hidden fixed inset-0 bg-red-900 bg-opacity-90 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative w-full max-w-md mx-auto p-6 sm:p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-600 mb-4">
                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">تحذير!</h3>
            <p class="text-red-100 mb-6">
                تم رصد تبديل التبويب. هذا مخالف لقواعد الامتحان.
            </p>
            <div id="warning-message" class="text-yellow-300 font-medium mb-6"></div>
            <button onclick="acknowledgeWarning()" 
                    class="bg-white text-red-600 px-6 py-3 rounded-lg font-bold hover:bg-gray-100 transition-colors">
                فهمت، أعود للامتحان
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentQuestion = 0;
let totalQuestions = {{ $questions->count() }};
let examId = {{ $exam->id }};
let attemptId = {{ $attempt->id }};
let timeRemaining = Math.max(0, {{ $attempt->remaining_time }});
let answers = {};
let timerInterval;
let questionTimerInterval;
let tabSwitchCount = 0;
let examEnded = false;
// وقت كل سؤال بالثواني (فهرس السؤال => ثوانٍ) — للأسئلة التي لها time_limit فقط
@php
    $questionTimeLimitsJson = [];
    foreach ($questions as $idx => $eq) {
        if (!empty($eq->time_limit) && (int)$eq->time_limit > 0) {
            $questionTimeLimitsJson[$idx] = (int) $eq->time_limit;
        }
    }
@endphp
let questionTimeLimits = @json($questionTimeLimitsJson);

// تهيئة الامتحان
document.addEventListener('DOMContentLoaded', function() {
    if (timeRemaining <= 0) {
        autoSubmitExam();
        return;
    }
    setupExamProtection();
    startTimer();
    loadSavedAnswers();
    startQuestionTimer(0);
    
    // منع العودة للخلف
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        if (!examEnded) {
            history.go(1);
            showTabSwitchWarning('محاولة العودة للخلف ممنوعة أثناء الامتحان');
        }
    };
});

// إبلاغ الخادم بمخالفة (سكرين شوت) أثناء الامتحان — يؤدي لتعليق الحساب فوراً
var examViolationReported = false;
function reportExamViolationToServer(type) {
    if (examViolationReported) return;
    examViolationReported = true;
    fetch('{{ route("my-courses.report-violation") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ type: type || 'screenshot', notes: 'امتحان', _token: '{{ csrf_token() }}' })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.suspended && data.redirect) {
            window.location.href = data.redirect;
        }
    })
    .catch(function() { examViolationReported = false; });
}

// كشف اختصارات التصوير (نفس منطق صفحة الدرس)
function isScreenshotShortcut(e) {
    var k = (e.key || '').toLowerCase();
    var c = e.keyCode || e.which;
    if (k === 'printscreen' || k === 'print' || k === 'snapshot' || c === 44) return true;
    if ((c === 83 || k === 's') && e.shiftKey && (e.metaKey || e.ctrlKey)) return true;
    if ((c === 83 || k === 's') && e.shiftKey && e.ctrlKey) return true;
    if (e.altKey && (c === 44 || k === 'printscreen' || k === 'print')) return true;
    if (e.metaKey && e.shiftKey && (c === 51 || c === 52 || c === 53 || k === '3' || k === '4' || k === '5')) return true;
    if ((e.metaKey || e.ctrlKey) && (c === 44 || k === 'printscreen' || k === 'print')) return true;
    return false;
}

// إعداد حماية الامتحان
function setupExamProtection() {
    // سكرين شوت = تعليق الحساب فوراً وتوجيه لصفحة الحساب الموقوف (بدون تحذير)
    window.addEventListener('keydown', function(e) {
        if (isScreenshotShortcut(e)) {
            e.preventDefault();
            e.stopPropagation();
            reportExamViolationToServer('screenshot');
            return false;
        }
    }, true);
    window.addEventListener('keyup', function(e) {
        if (isScreenshotShortcut(e)) {
            e.preventDefault();
            reportExamViolationToServer('screenshot');
            return false;
        }
    }, true);
    document.addEventListener('keydown', function(e) {
        if (isScreenshotShortcut(e)) {
            e.preventDefault();
            reportExamViolationToServer('screenshot');
            return false;
        }
    }, true);
    document.addEventListener('keyup', function(e) {
        if (isScreenshotShortcut(e)) {
            e.preventDefault();
            reportExamViolationToServer('screenshot');
            return false;
        }
    }, true);
    document.addEventListener('paste', function(e) {
        var items = e.clipboardData && e.clipboardData.items;
        if (items) {
            for (var i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    e.preventDefault();
                    reportExamViolationToServer('screenshot');
                    return;
                }
            }
        }
    }, true);

    // منع النقر بالزر الأيمن (يعتبر مخالفة أيضاً → تعليق)
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        reportExamViolationToServer('screenshot');
    }, true);

    // منع اختصارات أخرى (F12، أدوات المطور، حفظ) — تحذير فقط أو تعليق حسب السياسة؛ هنا نعاملها تحذيراً
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
            (e.ctrlKey && e.key === 'u') || (e.ctrlKey && e.key === 's')) {
            e.preventDefault();
            showTabSwitchWarning('هذا الإجراء ممنوع أثناء الامتحان');
            return false;
        }
    });

    // مراقبة تغيير النافذة
    document.addEventListener('visibilitychange', function() {
        if (document.hidden && !examEnded) {
            logTabSwitch();
        }
    });

    window.addEventListener('blur', function() {
        if (!examEnded) {
            logTabSwitch();
        }
    });

    // منع إغلاق النافذة
    window.addEventListener('beforeunload', function(e) {
        if (!examEnded) {
            e.preventDefault();
            e.returnValue = 'هل تريد مغادرة الامتحان؟ سيتم تسليم إجاباتك الحالية.';
            return e.returnValue;
        }
    });
}

// بدء العداد التنازلي للامتحان (بالثواني)
function startTimer() {
    updateTimerDisplay();
    timerInterval = setInterval(function() {
        timeRemaining--;
        updateTimerDisplay();
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            autoSubmitExam();
        }
    }, 1000);
}

// عداد وقت السؤال (عند وجود time_limit) — يبدأ عند الدخول للسؤال
function startQuestionTimer(questionIndex) {
    if (questionTimerInterval) {
        clearInterval(questionTimerInterval);
        questionTimerInterval = null;
    }
    var limit = questionTimeLimits[questionIndex];
    if (limit == null || limit <= 0) return;
    var remaining = limit;
    var el = document.getElementById('question-timer-' + questionIndex);
    if (!el) return;
    function update() {
        if (remaining <= 0) {
            if (questionTimerInterval) clearInterval(questionTimerInterval);
            questionTimerInterval = null;
            if (currentQuestion < totalQuestions - 1) {
                nextQuestion();
            } else {
                submitExam();
            }
            return;
        }
        var m = Math.floor(remaining / 60);
        var s = remaining % 60;
        el.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        if (remaining <= 30) el.classList.add('text-red-400', 'animate-pulse');
        remaining--;
    }
    update();
    questionTimerInterval = setInterval(update, 1000);
}

function stopQuestionTimer() {
    if (questionTimerInterval) {
        clearInterval(questionTimerInterval);
        questionTimerInterval = null;
    }
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = Math.floor(timeRemaining % 60);
    const timerText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    const timerEl = document.getElementById('timer');
    const submitTimerEl = document.getElementById('submit-timer');
    if (timerEl) timerEl.textContent = timerText;
    if (submitTimerEl) submitTimerEl.textContent = timerText;
    
    // تغيير لون العداد عند اقتراب انتهاء الوقت
    if (timerEl) {
        if (timeRemaining <= 300) {
            timerEl.className = 'text-2xl sm:text-3xl font-black tabular-nums tracking-wider text-red-400 animate-pulse';
        } else if (timeRemaining <= 600) {
            timerEl.className = 'text-2xl sm:text-3xl font-black tabular-nums tracking-wider text-amber-400';
        } else {
            timerEl.className = 'text-2xl sm:text-3xl font-black tabular-nums tracking-wider text-amber-400';
        }
    }
}

// الانتقال بين الأسئلة
function goToQuestion(index) {
    // إخفاء السؤال الحالي
    document.getElementById(`question-${currentQuestion}`).classList.add('hidden');
    const prevNav = document.getElementById(`question-nav-${currentQuestion}`);
    if (prevNav) {
        prevNav.classList.remove('bg-indigo-600', 'text-white');
        prevNav.classList.add('bg-gray-700', 'text-gray-300');
    }
    
    // إظهار السؤال الجديد
    currentQuestion = index;
    document.getElementById(`question-${currentQuestion}`).classList.remove('hidden');
    const currNav = document.getElementById(`question-nav-${currentQuestion}`);
    if (currNav) {
        currNav.classList.remove('bg-gray-700', 'text-gray-300');
        currNav.classList.add('bg-indigo-600', 'text-white');
    }
    
    // تحديث شريط التقدم في الهيدر
    const headerProgressBar = document.getElementById('header-progress-bar');
    const headerProgressText = document.getElementById('header-progress-text');
    if (headerProgressBar) {
        headerProgressBar.style.width = ((currentQuestion + 1) / totalQuestions * 100) + '%';
    }
    if (headerProgressText) {
        headerProgressText.textContent = (currentQuestion + 1) + ' / ' + totalQuestions;
    }
    
    // تحديث أزرار التنقل
    const prevBtn = document.getElementById('prev-btn');
    prevBtn.disabled = (currentQuestion === 0);
    prevBtn.className = currentQuestion === 0 ? 
        'order-2 sm:order-1 px-6 py-3 bg-gray-600 text-white rounded-xl font-semibold opacity-50 cursor-not-allowed' :
        'order-2 sm:order-1 px-6 py-3 bg-gray-600 hover:bg-gray-500 text-white rounded-xl font-semibold transition-colors';
        
    const nextBtn = document.getElementById('next-btn');
    nextBtn.innerHTML = currentQuestion === totalQuestions - 1 ? 'إنهاء <i class="fas fa-arrow-left mr-2"></i>' : 'التالي <i class="fas fa-arrow-left mr-2"></i>';

    // عداد وقت السؤال (إن وُجد)
    stopQuestionTimer();
    startQuestionTimer(currentQuestion);
}

function nextQuestion() {
    if (currentQuestion < totalQuestions - 1) {
        goToQuestion(currentQuestion + 1);
    } else {
        confirmSubmit();
    }
}

function previousQuestion() {
    if (currentQuestion > 0) {
        goToQuestion(currentQuestion - 1);
    }
}

// حفظ الإجابة
function saveAnswer(questionId, answer) {
    answers[questionId] = answer;
    
    // تحديث حالة السؤال في القائمة الجانبية
    const statusIndicator = document.getElementById(`question-status-${currentQuestion}`);
    statusIndicator.className = 'w-4 h-4 rounded-full bg-green-500';
    
    // إرسال الإجابة للخادم
    fetch(`{{ route('student.exams.save-answer', [$exam, $attempt]) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            question_id: questionId,
            answer: answer
        })
    }).catch(error => {
        console.error('Error saving answer:', error);
    });
    
    updateProgress();
}

function updateProgress() {
    const answeredCount = Object.keys(answers).length;
    document.getElementById('progress-text').textContent = `${answeredCount} / ${totalQuestions}`;
    document.getElementById('answered-count').textContent = answeredCount;
}

function loadSavedAnswers() {
    // تحميل الإجابات المحفوظة من المحاولة
    @if($attempt->answers)
        const savedAnswers = @json($attempt->answers);
        for (let questionId in savedAnswers) {
            answers[questionId] = savedAnswers[questionId];
            
            // تحديث واجهة المستخدم
            const answerInput = document.querySelector(`[name="answer_${questionId}"][value="${savedAnswers[questionId]}"]`) ||
                               document.getElementById(`answer_${questionId}`);
            
            if (answerInput) {
                if (answerInput.type === 'radio') {
                    answerInput.checked = true;
                } else {
                    answerInput.value = savedAnswers[questionId];
                }
            }
        }
        updateProgress();
    @endif
}

// تسجيل تبديل التبويب
function logTabSwitch() {
    tabSwitchCount++;
    
    fetch(`{{ route('student.exams.tab-switch', [$exam, $attempt]) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.exam_ended) {
            examEnded = true;
            clearInterval(timerInterval);
            alert(data.message);
            window.location.href = '{{ route("student.exams.index") }}';
        } else if (data.warning) {
            showTabSwitchWarning(data.message);
        }
    })
    .catch(error => {
        console.error('Error logging tab switch:', error);
    });
}

function showTabSwitchWarning(message) {
    document.getElementById('warning-message').textContent = message;
    document.getElementById('tabSwitchWarning').classList.remove('hidden');
}

function acknowledgeWarning() {
    document.getElementById('tabSwitchWarning').classList.add('hidden');
}

// تأكيد التسليم
function confirmSubmit() {
    updateProgress();
    document.getElementById('submitModal').classList.remove('hidden');
}

function closeSubmitModal() {
    document.getElementById('submitModal').classList.add('hidden');
}

function submitExam() {
    examEnded = true;
    clearInterval(timerInterval);
    
    // إرسال نموذج التسليم
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("student.exams.submit", [$exam, $attempt]) }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    document.body.appendChild(form);
    form.submit();
}

function autoSubmitExam() {
    examEnded = true;
    clearInterval(timerInterval);
    
    alert('انتهى الوقت المحدد للامتحان. سيتم تسليم إجاباتك تلقائياً.');
    
    // تسليم تلقائي
    fetch(`{{ route('student.exams.submit', [$exam, $attempt]) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        if (response.ok) {
            window.location.href = '{{ route("student.exams.index") }}';
        }
    })
    .catch(error => {
        console.error('Error auto-submitting exam:', error);
        window.location.href = '{{ route("student.exams.index") }}';
    });
}

// منع التلاعب
Object.defineProperty(console, 'log', {
    value: function() {
        logTabSwitch();
    }
});
</script>

<style>
/* إخفاء شريط التمرير وحماية إضافية */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #374151;
}

::-webkit-scrollbar-thumb {
    background: #6b7280;
    border-radius: 3px;
}

/* منع التحديد */
* {
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
    -webkit-user-drag: none !important;
}

/* السماح بالتحديد في حقول الإدخال فقط */
input, textarea {
    -webkit-user-select: text !important;
    -moz-user-select: text !important;
    -ms-user-select: text !important;
    user-select: text !important;
}

/* منع الطباعة */
@media print {
    body { display: none !important; }
}
</style>
@endpush
@endsection

@extends('layouts.app')

@section('title', $exam->title)
@section('header', $exam->title)

@section('content')
<div class="w-full max-w-full min-w-0 space-y-4 sm:space-y-6 -mx-4 px-3 sm:mx-0 sm:px-0">
    <!-- الهيدر -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <nav class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 flex flex-wrap items-center gap-1">
            <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">لوحة التحكم</a>
            <span>/</span>
            <a href="{{ route('student.exams.index') }}" class="hover:text-indigo-600">امتحاناتي</a>
            <span>/</span>
            <span class="text-gray-700 dark:text-gray-300 truncate max-w-[180px] sm:max-w-none">{{ $exam->title }}</span>
        </nav>
        <a href="{{ route('student.exams.index') }}" 
           class="inline-flex items-center justify-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-xl font-medium transition-colors w-full sm:w-auto flex-shrink-0">
            <i class="fas fa-arrow-right"></i>
            العودة
        </a>
    </div>

    <!-- معلومات الامتحان -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
        <!-- المحتوى الرئيسي -->
        <div class="xl:col-span-2 space-y-4 sm:space-y-6 min-w-0">
            <!-- تفاصيل الامتحان -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $exam->title }}</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $exam->isAvailable() ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            {{ $exam->isAvailable() ? 'متاح الآن' : 'غير متاح' }}
                        </span>
                    </div>
                </div>
                
                <div class="p-4 sm:p-6">
                    @if($exam->description)
                        <div class="mb-4 sm:mb-6">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">وصف الامتحان</h4>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">{{ $exam->description }}</p>
                        </div>
                    @endif

                    @if($exam->instructions)
                        <div class="mb-4 sm:mb-6">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">تعليمات الامتحان</h4>
                            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-4">
                                <div class="text-indigo-900 dark:text-indigo-100 text-sm sm:text-base whitespace-pre-wrap break-words">{{ $exam->instructions }}</div>
                            </div>
                        </div>
                    @endif

                    <!-- معلومات تفصيلية -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">الكورس</label>
                                <div class="text-gray-900 dark:text-white">{{ $exam->course->title }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $exam->course->academicSubject->name ?? 'غير محدد' }}</div>
                            </div>
                            
                            @if($exam->lesson)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">الدرس</label>
                                    <div class="text-gray-900 dark:text-white">{{ $exam->lesson->title }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">المدة</label>
                                <div class="text-gray-900 dark:text-white">{{ $exam->duration_minutes }} دقيقة</div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">عدد الأسئلة</label>
                                <div class="text-gray-900 dark:text-white">{{ $exam->examQuestions->count() }} سؤال</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- المحاولات السابقة -->
            @if($previousAttempts->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">محاولاتك السابقة</h4>
                    </div>
                    <div class="p-4 sm:p-6 overflow-x-auto -mx-4 sm:mx-0">
                        <div class="min-w-[500px] sm:min-w-0">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-3 sm:px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300">المحاولة</th>
                                        <th class="px-3 sm:px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300">النتيجة</th>
                                        <th class="px-3 sm:px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 hidden sm:table-cell">الوقت</th>
                                        <th class="px-3 sm:px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300">التاريخ</th>
                                        <th class="px-3 sm:px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($previousAttempts as $index => $attempt)
                                        <tr>
                                            <td class="px-3 sm:px-4 py-2 sm:py-3 text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                                            <td class="px-3 sm:px-4 py-2 sm:py-3">
                                                @if(in_array($attempt->status, ['submitted', 'auto_submitted']))
                                                    <span class="font-medium {{ $attempt->result_color == 'green' ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ number_format($attempt->percentage, 1) }}%
                                                    </span>
                                                @else
                                                    <span class="text-gray-500 dark:text-gray-400">غير مكتمل</span>
                                                @endif
                                            </td>
                                            <td class="px-3 sm:px-4 py-2 sm:py-3 text-gray-500 dark:text-gray-400 hidden sm:table-cell">{{ $attempt->formatted_time }}</td>
                                            <td class="px-3 sm:px-4 py-2 sm:py-3 text-gray-500 dark:text-gray-400 text-xs sm:text-sm">{{ $attempt->created_at->format('d/m/Y') }}</td>
                                            <td class="px-3 sm:px-4 py-2 sm:py-3">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    {{ $attempt->result_color == 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                       ($attempt->result_color == 'red' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                                        'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200') }}">
                                                    {{ $attempt->result_status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- الشريط الجانبي -->
        <div class="xl:col-span-1 space-y-4 sm:space-y-6 min-w-0">
            <!-- معلومات الامتحان -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">معلومات الامتحان</h4>
                </div>
                <div class="p-4 sm:p-6 space-y-3 sm:space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">مدة الامتحان</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $exam->duration_minutes }} دقيقة</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">عدد الأسئلة</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $exam->examQuestions->count() }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">إجمالي الدرجات</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $exam->total_marks }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">درجة النجاح</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $exam->passing_marks }}%</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">المحاولات المسموحة</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $exam->attempts_allowed == 0 ? 'غير محدود' : $exam->attempts_allowed }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">محاولاتك</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $previousAttempts->count() }}</span>
                    </div>

                    @if($exam->start_time)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">يبدأ في</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $exam->start_time->format('Y-m-d H:i') }}</span>
                        </div>
                    @endif

                    @if($exam->end_time)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">ينتهي في</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $exam->end_time->format('Y-m-d H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- متطلبات الامتحان -->
            @if($exam->prevent_tab_switch || $exam->require_camera || $exam->require_microphone)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <div class="px-6 py-4 border-b border-yellow-200 dark:border-yellow-800">
                        <h4 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">
                            <i class="fas fa-shield-alt ml-2"></i>
                            متطلبات الأمان
                        </h4>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-2 text-sm text-yellow-800 dark:text-yellow-200">
                            @if($exam->prevent_tab_switch)
                                <li class="flex items-center">
                                    <i class="fas fa-exclamation-triangle ml-2"></i>
                                    ممنوع تبديل التبويبات أثناء الامتحان
                                </li>
                            @endif
                            @if($exam->require_camera)
                                <li class="flex items-center">
                                    <i class="fas fa-video ml-2"></i>
                                    يتطلب تفعيل الكاميرا للمراقبة
                                </li>
                            @endif
                            @if($exam->require_microphone)
                                <li class="flex items-center">
                                    <i class="fas fa-microphone ml-2"></i>
                                    يتطلب تفعيل المايكروفون للمراقبة
                                </li>
                            @endif
                            @if($exam->auto_submit)
                                <li class="flex items-center">
                                    <i class="fas fa-clock ml-2"></i>
                                    سيتم تسليم الامتحان تلقائياً عند انتهاء الوقت
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endif

            <!-- زر بدء الامتحان -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-center">
                    @if($exam->examQuestions->isEmpty())
                        <div class="text-center">
                            <i class="fas fa-info-circle text-4xl text-amber-500 mb-4"></i>
                            <h4 class="text-xl font-bold text-amber-900 dark:text-amber-100 mb-2">الامتحان غير جاهز بعد</h4>
                            <p class="text-amber-700 dark:text-amber-300">لم تتم إضافة أسئلة لهذا الامتحان بعد. يرجى المحاولة لاحقاً.</p>
                        </div>
                    @elseif($exam->canAttempt(auth()->id()))
                        <div class="mb-4">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">جاهز لبدء الامتحان؟</h4>
                            <p class="text-gray-600 dark:text-gray-400">
                                تأكد من قراءة جميع التعليمات قبل البدء. لن تتمكن من العودة بعد البدء.
                            </p>
                        </div>
                        
                        <form action="{{ route('student.exams.start', $exam) }}" method="POST" id="start-exam-form">
                            @csrf
                            <button type="button" onclick="confirmStart()" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 sm:px-8 py-3 rounded-xl font-bold text-base sm:text-lg transition-colors">
                                <i class="fas fa-play"></i>
                                ابدأ الامتحان الآن
                            </button>
                        </form>
                    @else
                        <div class="text-center">
                            <i class="fas fa-times-circle text-4xl text-red-500 mb-4"></i>
                            <h4 class="text-xl font-bold text-red-900 dark:text-red-100 mb-2">غير متاح للبدء</h4>
                            <p class="text-red-700 dark:text-red-300">
                                @if($previousAttempts->count() >= $exam->attempts_allowed && $exam->attempts_allowed > 0)
                                    لقد استنفدت عدد المحاولات المسموحة ({{ $exam->attempts_allowed }})
                                @elseif(!$exam->isAvailable())
                                    الامتحان غير متاح حالياً
                                @else
                                    غير مصرح لك بأداء هذا الامتحان
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي - النتائج -->
        <div class="xl:col-span-1 space-y-4 sm:space-y-6 min-w-0">
            <!-- أفضل النتائج -->
            @if($previousAttempts->whereIn('status', ['submitted', 'auto_submitted'])->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">نتائجك</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        @php
                            $completedAttempts = $previousAttempts->whereIn('status', ['submitted', 'auto_submitted']);
                            $bestScore = $completedAttempts->max('percentage');
                            $lastAttempt = $completedAttempts->first();
                        @endphp
                        
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($bestScore, 1) }}%</div>
                            <div class="text-sm text-blue-700 dark:text-blue-300">أفضل نتيجة</div>
                        </div>
                        
                        @if($lastAttempt)
                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($lastAttempt->percentage, 1) }}%</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">آخر محاولة</div>
                                <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $lastAttempt->created_at->diffForHumans() }}</div>
                            </div>
                        @endif

                        @if($exam->show_results_immediately && $lastAttempt)
                            <a href="{{ route('student.exams.result', [$exam, $lastAttempt]) }}" 
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                                <i class="fas fa-chart-line ml-2"></i>
                                عرض آخر نتيجة
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- معلومات إضافية -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">تفاصيل إضافية</h4>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">خلط الأسئلة</span>
                        <span class="text-sm font-medium {{ $exam->randomize_questions ? 'text-green-600' : 'text-red-600' }}">
                            {{ $exam->randomize_questions ? 'مفعل' : 'معطل' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">عرض النتيجة فوراً</span>
                        <span class="text-sm font-medium {{ $exam->show_results_immediately ? 'text-green-600' : 'text-red-600' }}">
                            {{ $exam->show_results_immediately ? 'نعم' : 'لا' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">عرض الإجابات الصحيحة</span>
                        <span class="text-sm font-medium {{ $exam->show_correct_answers ? 'text-green-600' : 'text-red-600' }}">
                            {{ $exam->show_correct_answers ? 'نعم' : 'لا' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">مراجعة الإجابات</span>
                        <span class="text-sm font-medium {{ $exam->allow_review ? 'text-green-600' : 'text-red-600' }}">
                            {{ $exam->allow_review ? 'مسموح' : 'غير مسموح' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة تأكيد البدء -->
<div id="confirmModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-70 overflow-y-auto h-full w-full z-50 flex items-start sm:items-center justify-center p-4">
    <div class="relative w-full max-w-md mx-auto p-4 sm:p-5 border shadow-xl rounded-2xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900">
                <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-4">تأكيد بدء الامتحان</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    هل أنت متأكد من بدء الامتحان؟ لن تتمكن من العودة أو إيقاف الامتحان بعد البدء.
                </p>
                
                @if($exam->prevent_tab_switch)
                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded">
                        <p class="text-sm text-red-700 dark:text-red-300 font-medium">
                            <i class="fas fa-warning ml-1"></i>
                            تحذير: ممنوع تبديل التبويبات أثناء الامتحان
                        </p>
                    </div>
                @endif
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3 px-4 py-3">
                <button type="button" onclick="startExam()" 
                        class="px-5 py-2.5 bg-indigo-600 text-white text-base font-medium rounded-xl hover:bg-indigo-700 transition-colors min-w-[100px]">
                    ابدأ
                </button>
                <button type="button" onclick="closeModal()" 
                        class="px-5 py-2.5 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-base font-medium rounded-xl hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors min-w-[100px]">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmStart() {
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('confirmModal').classList.add('hidden');
}

function startExam() {
    document.getElementById('start-exam-form').submit();
}

// إغلاق النافذة عند النقر خارجها
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endpush
@endsection

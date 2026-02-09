@extends('layouts.app')

@section('title', 'أسئلة الامتحان - ' . $exam->title)
@section('header', 'أسئلة الامتحان')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('admin.exams.index') }}" class="hover:text-indigo-600">الامتحانات</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.exams.show', $exam) }}" class="hover:text-indigo-600">{{ Str::limit($exam->title, 30) }}</a>
                <span class="mx-2">/</span>
                <span class="text-gray-700 dark:text-gray-300">الأسئلة</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $exam->title }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">إجمالي الدرجات: {{ $exam->total_marks ?? 0 }} — عدد الأسئلة: {{ $exam->examQuestions->count() }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="document.getElementById('add-question-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                <i class="fas fa-plus"></i>
                إضافة سؤال
            </button>
            <a href="{{ route('admin.exams.show', $exam) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة للامتحان
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-800 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- أسئلة الامتحان الحالية -->
        <div class="xl:col-span-1 bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col" style="min-height: 75vh;">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-list-ol text-gray-500 dark:text-gray-400"></i>
                    أسئلة الامتحان ({{ $exam->examQuestions->count() }})
                </h2>
            </div>
            <div class="p-4 sm:p-6 flex-1 overflow-y-auto">
                @if($exam->examQuestions->count() > 0)
                    <ul class="space-y-3" id="exam-questions-list">
                        @foreach($exam->examQuestions as $eq)
                        <li class="flex items-start justify-between gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2">{{ $eq->question ? Str::limit(strip_tags($eq->question->question ?? ''), 100) ?: '—' : '—' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $eq->question->category->name ?? '—' }} — {{ $eq->marks }} درجة
                                    @if($eq->time_limit) — {{ $eq->time_limit }} ثانية @endif
                                </p>
                            </div>
                            <form action="{{ route('admin.exams.questions.remove', [$exam, $eq]) }}" method="POST" class="shrink-0" onsubmit="return confirm('إزالة هذا السؤال من الامتحان؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="إزالة">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">لا توجد أسئلة في الامتحان بعد. أضف أسئلة من بنك الأسئلة.</p>
                @endif
            </div>
        </div>

        <!-- إضافة سؤال من بنك الأسئلة (واجهة أوسع + فلاتر) -->
        <div class="xl:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col" style="min-height: 75vh;">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2 mb-4">
                    <i class="fas fa-database text-gray-500 dark:text-gray-400"></i>
                    إضافة سؤال من بنك الأسئلة
                </h2>
                {{-- فلاتر: سنة دراسية، مادة، تصنيف --}}
                <form method="GET" action="{{ route('admin.exams.questions.manage', $exam) }}" class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[140px]">
                        <label for="filter_year" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">السنة الدراسية</label>
                        <select name="academic_year_id" id="filter_year" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            <option value="">— الكل —</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ request('academic_year_id') == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[140px]">
                        <label for="filter_subject" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">المادة</label>
                        <select name="academic_subject_id" id="filter_subject" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            <option value="">— الكل —</option>
                            @foreach($academicSubjects as $sub)
                                <option value="{{ $sub->id }}" {{ request('academic_subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }} @if($sub->academicYear) ({{ $sub->academicYear->name }}) @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[180px]">
                        <label for="filter_category" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">التصنيف</label>
                        <select name="category_id" id="filter_category" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                            <option value="">— الكل —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }} @if($cat->academicYear) ({{ $cat->academicYear->name }}) @endif — {{ $cat->questions->count() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-filter ml-1"></i>
                        تطبيق الفلتر
                    </button>
                    @if(request()->hasAny(['academic_year_id','academic_subject_id','category_id']))
                        <a href="{{ route('admin.exams.questions.manage', $exam) }}" class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">إلغاء الفلتر</a>
                    @endif
                </form>
                {{-- إضافة مجموعة: نموذج يربط به الـ checkboxes عبر form="bulk-add-form" --}}
                @php $examQuestionIds = $exam->examQuestions->pluck('question_id')->toArray(); $hasAvailableQuestions = $categories->pluck('questions')->flatten()->filter(fn($q) => !in_array($q->id, $examQuestionIds))->count() > 0; @endphp
                @if($hasAvailableQuestions)
                <form id="bulk-add-form" method="POST" action="{{ route('admin.exams.questions.bulk', $exam) }}" class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    @csrf
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <span>درجة كل سؤال (للمحدد):</span>
                        <input type="number" name="default_marks" value="1" min="0.5" step="0.5" max="100" class="w-20 px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </label>
                    <button type="submit" id="bulk-add-btn" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" title="أضف الأسئلة المحددة">
                        <i class="fas fa-layer-group"></i>
                        إضافة المحدد (<span id="bulk-selected-count">0</span>)
                    </button>
                    <button type="button" onclick="toggleAllBulkCheckboxes(true)" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">تحديد الكل</button>
                    <button type="button" onclick="toggleAllBulkCheckboxes(false)" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">إلغاء التحديد</button>
                </form>
                @endif
            </div>
            <div class="p-4 sm:p-6 flex-1 overflow-y-auto min-h-0">
                @if($categories->count() > 0)
                    @foreach($categories as $category)
                        @if($category->questions->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-gray-600 flex items-center gap-2">
                                <i class="fas fa-folder-open text-indigo-500"></i>
                                {{ $category->name }}
                                @if($category->academicYear || $category->academicSubject)
                                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">
                                        @if($category->academicYear) {{ $category->academicYear->name }} @endif
                                        @if($category->academicSubject) — {{ $category->academicSubject->name }} @endif
                                    </span>
                                @endif
                                <span class="text-xs bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded-full">{{ $category->questions->count() }} سؤال</span>
                            </h3>
                            <ul class="space-y-3">
                                @foreach($category->questions as $q)
                                    @if(!in_array($q->id, $examQuestionIds))
                                    <li class="flex items-center justify-between gap-3 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors group">
                                        <div class="min-w-0 flex-1 flex items-center gap-3">
                                            @if($hasAvailableQuestions)
                                            <input type="checkbox" name="question_ids[]" value="{{ $q->id }}" form="bulk-add-form" class="bulk-checkbox rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 shrink-0" onchange="updateBulkCount()">
                                            @endif
                                            <span class="text-sm text-gray-900 dark:text-white line-clamp-2 flex-1">{{ Str::limit(strip_tags($q->question ?? ''), 120) ?: '—' }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 shrink-0">{{ $q->type_text ?? $q->type }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <button type="button" onclick="openQuestionPreview({{ $q->id }})" class="p-2 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors" title="عرض السؤال">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <form action="{{ route('admin.exams.questions.add', $exam) }}" method="POST" class="flex items-center gap-2">
                                                @csrf
                                                <input type="hidden" name="question_id" value="{{ $q->id }}">
                                                <input type="number" name="marks" value="1" min="0.5" step="0.5" max="100" class="w-20 px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                                <button type="submit" class="p-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors" title="إضافة">
                                                    <i class="fas fa-plus text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                        {{-- محتوى المعاينة (مخفي) --}}
                                        <div id="preview-content-{{ $q->id }}" class="hidden">
                                            <div class="text-base font-medium text-gray-900 dark:text-white mb-2">{{ $q->question ?: '—' }}</div>
                                            @if($q->image_url)
                                                <div class="mb-2"><img src="{{ $q->getImageUrl() }}" alt="صورة السؤال" class="max-w-full max-h-48 rounded-lg object-contain border border-gray-200 dark:border-gray-600"></div>
                                            @endif
                                            @if($q->options && is_array($q->options))
                                                <ul class="list-disc list-inside text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                                    @foreach($q->options as $opt)
                                                        <li>{{ is_array($opt) ? implode(' / ', $opt) : $opt }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">النوع: {{ $q->type_text ?? $q->type }} — التصنيف: {{ $q->category->name ?? '—' }}</p>
                                        </div>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    @endforeach
                    @if($categories->pluck('questions')->flatten()->filter(fn($q) => !in_array($q->id, $examQuestionIds))->count() === 0)
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-12">جميع أسئلة التصنيفات المفلترة مضافة بالفعل للامتحان، أو لا توجد أسئلة متاحة. غيّر الفلتر أو أضف أسئلة من بنك الأسئلة.</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-12">لا توجد تصنيفات أو أسئلة تطابق الفلتر. غيّر الفلتر أو أنشئ تصنيفات وأسئلة من بنك الأسئلة.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- نافذة منبثقة: إضافة سؤال (أكبر) -->
<div id="add-question-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('add-question-modal').classList.add('hidden')" aria-hidden="true"></div>
        <div class="relative inline-block w-full max-w-5xl max-h-[95vh] overflow-hidden text-right align-middle bg-white dark:bg-gray-800 rounded-2xl shadow-xl transform transition-all flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80 shrink-0">
                <h3 id="modal-title" class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-plus-circle text-indigo-500"></i>
                    إضافة سؤال للامتحان
                </h3>
                <button type="button" onclick="document.getElementById('add-question-modal').classList.add('hidden')" class="p-2 rounded-lg text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex border-b border-gray-200 dark:border-gray-700 shrink-0">
                <button type="button" id="tab-from-bank" onclick="switchAddTab('from-bank')" class="flex-1 px-4 py-3 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-900/20">
                    <i class="fas fa-database ml-2"></i>
                    من البنك
                </button>
                <button type="button" id="tab-new-question" onclick="switchAddTab('new-question')" class="flex-1 px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <i class="fas fa-plus ml-2"></i>
                    سؤال جديد
                </button>
            </div>
            <div class="overflow-y-auto flex-1 min-h-0 p-4 sm:p-6">
                <div id="panel-from-bank" class="space-y-4">
                    <input type="text" id="modal-search" placeholder="ابحث في الأسئلة أو التصنيف..." oninput="filterModalQuestions()" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @if($categories->pluck('questions')->flatten()->filter(fn($q) => !in_array($q->id, $examQuestionIds))->count() > 0)
                    <form id="modal-bulk-add-form" method="POST" action="{{ route('admin.exams.questions.bulk', $exam) }}" class="flex flex-wrap items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                        @csrf
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <span>درجة كل سؤال (للمحدد):</span>
                            <input type="number" name="default_marks" value="1" min="0.5" step="0.5" max="100" class="w-16 px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        </label>
                        <button type="submit" id="modal-bulk-add-btn" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-layer-group"></i>
                            إضافة المحدد (<span id="modal-bulk-selected-count">0</span>)
                        </button>
                        <button type="button" onclick="toggleAllModalBulkCheckboxes(true)" class="text-xs text-gray-600 dark:text-gray-400 hover:text-indigo-600">تحديد الكل</button>
                        <button type="button" onclick="toggleAllModalBulkCheckboxes(false)" class="text-xs text-gray-600 dark:text-gray-400 hover:text-indigo-600">إلغاء التحديد</button>
                    </form>
                    @endif
                    <div id="modal-questions-list" class="space-y-4">
                        @foreach($categories as $category)
                            @if($category->questions->count() > 0)
                            <div class="modal-category" data-category="{{ $category->name }}">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 pb-2 border-b border-gray-200 dark:border-gray-600 sticky top-0 bg-white dark:bg-gray-800 py-2 z-10">
                                    {{ $category->name }}
                                    @if($category->academicYear) <span class="text-gray-500">({{ $category->academicYear->name }})</span> @endif
                                </h4>
                                <ul class="space-y-2">
                                    @foreach($category->questions as $q)
                                        @if(!in_array($q->id, $examQuestionIds))
                                        <li class="modal-question-item flex items-center justify-between gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors" data-text="{{ Str::limit(strip_tags($q->question ?? ''), 300) }}">
                                            <div class="min-w-0 flex-1 flex items-center gap-2">
                                                <input type="checkbox" name="question_ids[]" value="{{ $q->id }}" form="modal-bulk-add-form" class="modal-bulk-checkbox rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 shrink-0" onchange="updateModalBulkCount()">
                                                <span class="text-sm text-gray-900 dark:text-white line-clamp-2 flex-1">{{ Str::limit(strip_tags($q->question ?? ''), 80) ?: '—' }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <button type="button" onclick="openQuestionPreview({{ $q->id }})" class="p-2 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20" title="عرض السؤال">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </button>
                                                <form action="{{ route('admin.exams.questions.add', $exam) }}" method="POST" class="flex items-center gap-2">
                                                    @csrf
                                                    <input type="hidden" name="question_id" value="{{ $q->id }}">
                                                    <input type="number" name="marks" value="1" min="0.5" step="0.5" max="100" class="w-16 px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                                    <button type="submit" class="p-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700" title="إضافة">
                                                        <i class="fas fa-plus text-sm"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div id="modal-preview-content-{{ $q->id }}" class="hidden">
                                                <div class="text-base font-medium text-gray-900 dark:text-white mb-2">{{ $q->question ?: '—' }}</div>
                                                @if($q->image_url)
                                                    <div class="mb-2"><img src="{{ $q->getImageUrl() }}" alt="صورة السؤال" class="max-w-full max-h-48 rounded-lg object-contain border border-gray-200 dark:border-gray-600"></div>
                                                @endif
                                                @if($q->options && is_array($q->options))
                                                    <ul class="list-disc list-inside text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                                        @foreach($q->options as $opt)
                                                            <li>{{ is_array($opt) ? implode(' / ', $opt) : $opt }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">النوع: {{ $q->type_text ?? $q->type }} — التصنيف: {{ $q->category->name ?? '—' }}</p>
                                            </div>
                                        </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    @if($categories->pluck('questions')->flatten()->filter(fn($q) => !in_array($q->id, $examQuestionIds))->count() === 0)
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">لا توجد أسئلة متاحة. أنشئ أسئلة من بنك الأسئلة أو غيّر الفلتر.</p>
                    @endif
                </div>
                <div id="panel-new-question" class="hidden space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">إنشاء سؤال جديد في بنك الأسئلة ثم إضافته للامتحان من تبويب «من البنك» (حدّث الصفحة بعد الإنشاء).</p>
                    <a href="{{ route('admin.question-bank.create') }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-external-link-alt"></i>
                        فتح صفحة إنشاء سؤال جديد
                    </a>
                    <p class="text-xs text-gray-500 dark:text-gray-400">ستُفتح الصفحة في نافذة جديدة. بعد حفظ السؤال ارجع هنا وحدّث الصفحة ليرظهر في قائمة «من البنك».</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة معاينة السؤال -->
<div id="preview-question-modal" class="hidden fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-black/50" onclick="closeQuestionPreview()" aria-hidden="true"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-eye text-indigo-500"></i>
                    معاينة السؤال
                </h3>
                <button type="button" onclick="closeQuestionPreview()" class="p-2 rounded-lg text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="preview-question-body" class="p-6 overflow-y-auto text-gray-800 dark:text-gray-200">
                {{-- يُملأ عبر JS --}}
            </div>
        </div>
    </div>
</div>

<script>
function switchAddTab(tab) {
    document.getElementById('panel-from-bank').classList.toggle('hidden', tab !== 'from-bank');
    document.getElementById('panel-new-question').classList.toggle('hidden', tab !== 'new-question');
    document.getElementById('tab-from-bank').classList.toggle('border-indigo-600', tab === 'from-bank');
    document.getElementById('tab-from-bank').classList.toggle('text-indigo-600', tab === 'from-bank');
    document.getElementById('tab-from-bank').classList.toggle('bg-indigo-50/50', tab === 'from-bank');
    document.getElementById('tab-from-bank').classList.toggle('border-transparent', tab !== 'from-bank');
    document.getElementById('tab-from-bank').classList.toggle('text-gray-600', tab !== 'from-bank');
    document.getElementById('tab-from-bank').classList.toggle('dark:text-indigo-400', tab === 'from-bank');
    document.getElementById('tab-from-bank').classList.toggle('dark:bg-indigo-900/20', tab === 'from-bank');
    document.getElementById('tab-new-question').classList.toggle('border-indigo-600', tab === 'new-question');
    document.getElementById('tab-new-question').classList.toggle('text-indigo-600', tab === 'new-question');
    document.getElementById('tab-new-question').classList.toggle('bg-indigo-50/50', tab === 'new-question');
    document.getElementById('tab-new-question').classList.toggle('border-transparent', tab !== 'new-question');
    document.getElementById('tab-new-question').classList.toggle('text-gray-600', tab !== 'new-question');
    document.getElementById('tab-new-question').classList.toggle('dark:text-indigo-400', tab === 'new-question');
    document.getElementById('tab-new-question').classList.toggle('dark:bg-indigo-900/20', tab === 'new-question');
}
function filterModalQuestions() {
    var q = (document.getElementById('modal-search') || {}).value.toLowerCase().trim();
    document.querySelectorAll('#modal-questions-list .modal-question-item').forEach(function(el) {
        var text = (el.getAttribute('data-text') || '').toLowerCase();
        var catEl = el.closest('.modal-category');
        var catName = catEl ? (catEl.getAttribute('data-category') || '').toLowerCase() : '';
        var show = !q || text.indexOf(q) !== -1 || catName.indexOf(q) !== -1;
        el.style.display = show ? '' : 'none';
    });
    document.querySelectorAll('#modal-questions-list .modal-category').forEach(function(cat) {
        var hasVisible = Array.prototype.slice.call(cat.querySelectorAll('.modal-question-item')).some(function(el) { return el.style.display !== 'none'; });
        cat.style.display = hasVisible ? '' : 'none';
    });
}
function openQuestionPreview(questionId) {
    var src = document.getElementById('preview-content-' + questionId) || document.getElementById('modal-preview-content-' + questionId);
    var body = document.getElementById('preview-question-body');
    if (src && body) {
        body.innerHTML = src.innerHTML;
        document.getElementById('preview-question-modal').classList.remove('hidden');
    }
}
function closeQuestionPreview() {
    document.getElementById('preview-question-modal').classList.add('hidden');
}
function updateBulkCount() {
    var checkboxes = document.querySelectorAll('.bulk-checkbox');
    var n = Array.prototype.filter.call(checkboxes, function(c) { return c.checked; }).length;
    var el = document.getElementById('bulk-selected-count');
    if (el) el.textContent = n;
    var btn = document.getElementById('bulk-add-btn');
    if (btn) btn.disabled = n === 0;
}
function toggleAllBulkCheckboxes(checked) {
    document.querySelectorAll('.bulk-checkbox').forEach(function(c) { c.checked = checked; });
    updateBulkCount();
}
function updateModalBulkCount() {
    var checkboxes = document.querySelectorAll('.modal-bulk-checkbox');
    var n = Array.prototype.filter.call(checkboxes, function(c) { return c.checked; }).length;
    var el = document.getElementById('modal-bulk-selected-count');
    if (el) el.textContent = n;
    var btn = document.getElementById('modal-bulk-add-btn');
    if (btn) btn.disabled = n === 0;
}
function toggleAllModalBulkCheckboxes(checked) {
    document.querySelectorAll('.modal-bulk-checkbox').forEach(function(c) { c.checked = checked; });
    updateModalBulkCount();
}
document.addEventListener('DOMContentLoaded', function() {
    updateBulkCount();
    updateModalBulkCount();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('add-question-modal').classList.add('hidden');
        closeQuestionPreview();
    }
});
</script>
@endsection

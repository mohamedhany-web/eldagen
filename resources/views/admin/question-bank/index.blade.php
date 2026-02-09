@extends('layouts.app')

@section('title', 'بنك الأسئلة')
@section('header', 'بنك الأسئلة')

@section('content')
<div class="space-y-6">
    <!-- الهيدر والإحصائيات -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">إدارة وتنظيم بنك الأسئلة</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.question-categories.index') }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-tags ml-2"></i>
                        إدارة التصنيفات
                    </a>
                    <a href="{{ route('admin.question-bank.create') }}" 
                       class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة سؤال جديد
                    </a>
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600">{{ $stats['total_questions'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">إجمالي الأسئلة</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['active_questions'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">أسئلة نشطة</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ $stats['categories_count'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">تصنيفات</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ count($stats['by_type']) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">أنواع أسئلة</div>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="البحث في نص الأسئلة..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">التصنيف</label>
                <select name="category_id" id="category_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">جميع التصنيفات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->full_path }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نوع السؤال</label>
                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">جميع الأنواع</option>
                    @foreach($questionTypes as $key => $type)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="difficulty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">مستوى الصعوبة</label>
                <select name="difficulty" id="difficulty" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">جميع المستويات</option>
                    @foreach($difficultyLevels as $key => $level)
                        <option value="{{ $key }}" {{ request('difficulty') == $key ? 'selected' : '' }}>{{ $level }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-search ml-2"></i>
                    بحث
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة الأسئلة -->
    @if($questions->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الأسئلة ({{ $questions->total() }})</h3>
                    <div class="flex items-center gap-2">
                        <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors">
                            <i class="fas fa-download ml-1"></i>
                            تصدير
                        </button>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors">
                            <i class="fas fa-upload ml-1"></i>
                            استيراد
                        </button>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($questions as $question)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($question->type == 'multiple_choice') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($question->type == 'true_false') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($question->type == 'fill_blank') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($question->type == 'essay') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                        @endif">
                                        {{ $question->type_text }}
                                    </span>
                                    
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($question->difficulty_level == 'easy') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($question->difficulty_level == 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @endif">
                                        {{ $question->difficulty_text }}
                                    </span>

                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $question->points }} نقطة
                                    </span>

                                    @if($question->hasMedia())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                            @if($question->image_url)
                                                <i class="fas fa-image ml-1"></i>
                                            @elseif($question->audio_url)
                                                <i class="fas fa-volume-up ml-1"></i>
                                            @elseif($question->video_url)
                                                <i class="fas fa-video ml-1"></i>
                                            @else
                                                <i class="fas fa-paperclip ml-1"></i>
                                            @endif
                                            وسائط
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                    {{ Str::limit($question->question, 100) }}
                                </h4>

                                <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($question->category)
                                        <span class="flex items-center">
                                            <i class="fas fa-tag ml-1"></i>
                                            {{ $question->category->full_path }}
                                        </span>
                                    @endif
                                    
                                    <span class="flex items-center">
                                        <i class="fas fa-clock ml-1"></i>
                                        {{ $question->created_at->diffForHumans() }}
                                    </span>

                                    @if($question->tags)
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-hashtag ml-1"></i>
                                            @foreach($question->tags as $tag)
                                                <span class="bg-gray-100 dark:bg-gray-600 px-2 py-0.5 rounded text-xs">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 space-x-reverse ml-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $question->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $question->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                                
                                <a href="{{ route('admin.question-bank.show', $question) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors p-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="{{ route('admin.question-bank.edit', $question) }}" 
                                   class="text-indigo-600 hover:text-indigo-800 transition-colors p-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('admin.question-bank.duplicate', $question) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 transition-colors p-2"
                                            title="نسخ السؤال">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.question-bank.destroy', $question) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition-colors p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- التصفح -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $questions->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-question-circle text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد أسئلة</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">ابدأ ببناء بنك الأسئلة الخاص بك</p>
            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('admin.question-categories.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-tags ml-2"></i>
                    إنشاء تصنيف
                </a>
                <a href="{{ route('admin.question-bank.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة أول سؤال
                </a>
            </div>
        </div>
    @endif

    <!-- إحصائيات مفصلة -->
    @if($stats['by_type'])
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">توزيع الأسئلة حسب النوع</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($stats['by_type'] as $type => $count)
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $count }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $questionTypes[$type] ?? $type }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

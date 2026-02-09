@extends('layouts.app')

@section('title', 'إدارة الامتحانات')
@section('header', 'إدارة الامتحانات')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">إنشاء وإدارة الامتحانات للكورسات</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.question-bank.index') }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-database ml-2"></i>
                        بنك الأسئلة
                    </a>
                    <a href="{{ route('admin.exams.create') }}" 
                       class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus ml-2"></i>
                        إنشاء امتحان جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="البحث في عناوين الامتحانات..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الكورس</label>
                <select name="course_id" id="course_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">جميع الكورسات</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }} - {{ $course->academicSubject->name ?? 'غير محدد' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
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

    <!-- قائمة الامتحانات -->
    @if($exams->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($exams as $exam)
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow duration-300">
                <!-- هيدر البطاقة -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $exam->title }}</h3>
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $exam->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $exam->is_active ? 'نشط' : 'معطل' }}
                            </span>
                            @if($exam->is_published)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    منشور
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- محتوى البطاقة -->
                <div class="p-6">
                    @if($exam->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($exam->description, 100) }}</p>
                    @endif

                    <div class="space-y-2 text-sm">
                        <!-- الكورس -->
                        <div class="flex items-center">
                            <i class="fas fa-graduation-cap text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600 dark:text-gray-400">الكورس:</span>
                            <span class="text-gray-900 dark:text-white mr-2">{{ $exam->course->title ?? 'غير محدد' }}</span>
                        </div>

                        <!-- المادة -->
                        <div class="flex items-center">
                            <i class="fas fa-book text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600 dark:text-gray-400">المادة:</span>
                            <span class="text-gray-900 dark:text-white mr-2">{{ $exam->course->academicSubject->name ?? 'غير محدد' }}</span>
                        </div>

                        <!-- المدة -->
                        <div class="flex items-center">
                            <i class="fas fa-clock text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600 dark:text-gray-400">المدة:</span>
                            <span class="text-gray-900 dark:text-white mr-2">{{ $exam->duration_minutes }} دقيقة</span>
                        </div>

                        <!-- عدد الأسئلة -->
                        <div class="flex items-center">
                            <i class="fas fa-question-circle text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600 dark:text-gray-400">الأسئلة:</span>
                            <span class="text-gray-900 dark:text-white mr-2">{{ $exam->questions_count }} سؤال</span>
                        </div>

                        <!-- المحاولات -->
                        <div class="flex items-center">
                            <i class="fas fa-users text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600 dark:text-gray-400">المحاولات:</span>
                            <span class="text-gray-900 dark:text-white mr-2">{{ $exam->attempts_count }} محاولة</span>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                    <!-- الصف الأول -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex space-x-2 space-x-reverse">
                            <a href="{{ route('admin.exams.show', $exam) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-eye ml-1"></i>
                                عرض
                            </a>
                            <a href="{{ route('admin.exams.questions.manage', $exam) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-question-circle ml-1"></i>
                                الأسئلة
                            </a>
                        </div>
                        
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <button onclick="toggleExamStatus({{ $exam->id }})" 
                                    class="inline-flex items-center px-3 py-1.5 {{ $exam->is_active ? 'bg-red-100 hover:bg-red-200 text-red-700' : 'bg-green-100 hover:bg-green-200 text-green-700' }} text-xs font-medium rounded-lg transition-colors">
                                <i class="fas {{ $exam->is_active ? 'fa-pause' : 'fa-play' }} ml-1"></i>
                                {{ $exam->is_active ? 'إيقاف' : 'تفعيل' }}
                            </button>
                            <button onclick="toggleExamPublish({{ $exam->id }})" 
                                    class="inline-flex items-center px-3 py-1.5 {{ $exam->is_published ? 'bg-yellow-100 hover:bg-yellow-200 text-yellow-700' : 'bg-purple-100 hover:bg-purple-200 text-purple-700' }} text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-globe ml-1"></i>
                                {{ $exam->is_published ? 'إلغاء النشر' : 'نشر' }}
                            </button>
                        </div>
                    </div>

                    <!-- الصف الثاني -->
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-2 space-x-reverse">
                            <a href="{{ route('admin.exams.statistics', $exam) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-chart-bar ml-1"></i>
                                إحصائيات
                            </a>
                            <a href="{{ route('admin.exams.preview', $exam) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-teal-100 hover:bg-teal-200 text-teal-700 text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-eye ml-1"></i>
                                معاينة
                            </a>
                        </div>
                        
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <a href="{{ route('admin.exams.edit', $exam) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-edit ml-1"></i>
                                تعديل
                            </a>
                            <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الامتحان؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded-lg transition-colors">
                                    <i class="fas fa-trash ml-1"></i>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- التصفح -->
        <div class="mt-8">
            {{ $exams->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clipboard-check text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد امتحانات</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">ابدأ بإنشاء الامتحانات للكورسات</p>
            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('admin.question-bank.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-database ml-2"></i>
                    بنك الأسئلة
                </a>
                <a href="{{ route('admin.exams.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    إنشاء أول امتحان
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleExamStatus(examId) {
    if (confirm('هل تريد تغيير حالة هذا الامتحان؟')) {
        fetch(`/admin/exams/${examId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ في تغيير حالة الامتحان');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الامتحان');
        });
    }
}

function toggleExamPublish(examId) {
    if (confirm('هل تريد تغيير حالة نشر هذا الامتحان؟')) {
        fetch(`/admin/exams/${examId}/toggle-publish`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ في تغيير حالة النشر');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة النشر');
        });
    }
}
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'إدارة الكورسات')
@section('header', 'إدارة الكورسات')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إدارة الكورسات</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">إدارة وتنظيم الكورسات التعليمية</p>
            </div>
            <a href="{{ route('admin.advanced-courses.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>
                إضافة كورس جديد
            </a>
        </div>

        <!-- الفلاتر -->
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">البحث</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="البحث في عناوين الكورسات..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">السنة الدراسية</label>
                    <select name="academic_year_id" id="academic_year_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">جميع السنوات</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        بحث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- قائمة الكورسات -->
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-200 dark:border-gray-700">
                <!-- هيدر البطاقة -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $course->title }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $course->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                            {{ $course->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </div>
                </div>

                <!-- محتوى البطاقة -->
                <div class="px-6 py-4">
                    @if($course->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($course->description, 100) }}</p>
                    @endif

                    <div class="space-y-2">
                        <!-- السنة الدراسية -->
                        <div class="flex items-center text-sm">
                            <i class="fas fa-calendar text-gray-400 dark:text-gray-500 w-4 ml-2"></i>
                            <span class="text-gray-600 dark:text-gray-400">السنة:</span>
                            <span class="text-gray-900 dark:text-white mr-2">{{ $course->academicYear->name ?? 'غير محدد' }}</span>
                        </div>

                        <!-- المادة الدراسية -->
                        <div class="flex items-center text-sm">
                            <i class="fas fa-book text-gray-400 dark:text-gray-500 w-4 ml-2"></i>
                            <span class="text-gray-600 dark:text-gray-400">المادة:</span>
                            <span class="text-gray-900 dark:text-white mr-2">{{ $course->academicSubject->name ?? 'غير محدد' }}</span>
                        </div>

                        <!-- السعر -->
                        @if($course->price)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-money-bill text-gray-400 dark:text-gray-500 w-4 ml-2"></i>
                            <span class="text-gray-600 dark:text-gray-400">السعر:</span>
                            <span class="text-gray-900 dark:text-white mr-2">{{ number_format($course->price) }} ج.م</span>
                        </div>
                        @else
                        <div class="flex items-center text-sm">
                            <i class="fas fa-gift text-green-500 dark:text-green-400 w-4 ml-2"></i>
                            <span class="text-green-600 dark:text-green-400 font-medium">مجاني</span>
                        </div>
                        @endif

                        <!-- تاريخ الإنشاء -->
                        <div class="flex items-center text-sm">
                            <i class="fas fa-clock text-gray-400 dark:text-gray-500 w-4 ml-2"></i>
                            <span class="text-gray-600 dark:text-gray-400">تاريخ الإنشاء:</span>
                            <span class="text-gray-900 dark:text-white mr-2">{{ $course->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>

                <!-- إحصائيات سريعة -->
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $course->lessons_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">درس</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $course->enrollments_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">طالب</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $course->orders_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">طلب</div>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="px-6 py-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-600">
                    <!-- الصف الأول من الأزرار -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex space-x-2 space-x-reverse">
                            <a href="{{ route('admin.advanced-courses.show', $course) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-eye ml-1"></i>
                                عرض التفاصيل
                            </a>
                            <a href="{{ route('admin.courses.sections.index', $course) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-layer-group ml-1"></i>
                                الأقسام
                            </a>
                            <a href="{{ route('admin.courses.lessons.index', $course) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-play-circle ml-1"></i>
                                الدروس
                            </a>
                        </div>
                        
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <button onclick="toggleCourseStatus({{ $course->id }})" 
                                    class="inline-flex items-center px-3 py-1.5 {{ $course->is_active ? 'bg-red-100 hover:bg-red-200 text-red-700' : 'bg-green-100 hover:bg-green-200 text-green-700' }} text-xs font-medium rounded-lg transition-colors">
                                <i class="fas {{ $course->is_active ? 'fa-pause' : 'fa-play' }} ml-1"></i>
                                {{ $course->is_active ? 'إيقاف' : 'تفعيل' }}
                            </button>
                            <button onclick="toggleCourseFeatured({{ $course->id }})" 
                                    class="inline-flex items-center px-3 py-1.5 {{ $course->is_featured ? 'bg-yellow-100 hover:bg-yellow-200 text-yellow-700' : 'bg-purple-100 hover:bg-purple-200 text-purple-700' }} text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-star ml-1"></i>
                                {{ $course->is_featured ? 'إلغاء الترشيح' : 'ترشيح' }}
                            </button>
                        </div>
                    </div>

                    <!-- الصف الثاني من الأزرار -->
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-2 space-x-reverse">
                            <a href="{{ route('admin.advanced-courses.lesson-answers', $course) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-list-check ml-1"></i>
                                إجابات الطلاب
                            </a>
                            <a href="{{ route('admin.courses.lessons.create', $course) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-teal-100 hover:bg-teal-200 text-teal-700 text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-plus ml-1"></i>
                                إضافة درس
                            </a>
                            <a href="{{ route('admin.advanced-courses.orders', $course) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-shopping-cart ml-1"></i>
                                الطلبات
                            </a>
                        </div>
                        
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <a href="{{ route('admin.advanced-courses.edit', $course) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs font-medium rounded-lg transition-colors">
                                <i class="fas fa-edit ml-1"></i>
                                تعديل
                            </a>
                            <form method="POST" action="{{ route('admin.advanced-courses.destroy', $course) }}" class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الكورس؟')">
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
            {{ $courses->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center border border-gray-200 dark:border-gray-700">
            <div class="text-gray-500 dark:text-gray-400">
                <i class="fas fa-graduation-cap text-4xl mb-4"></i>
                <p class="text-lg font-medium text-gray-900 dark:text-white">لا توجد كورسات</p>
                <p class="text-sm mt-2">لم يتم العثور على أي كورسات تطابق معايير البحث</p>
            </div>
            <div class="mt-6">
                <a href="{{ route('admin.advanced-courses.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    إضافة أول كورس
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleCourseStatus(courseId) {
    if (confirm('هل تريد تغيير حالة هذا الكورس؟')) {
        fetch(`/admin/advanced-courses/${courseId}/toggle-status`, {
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
                alert('حدث خطأ في تغيير حالة الكورس');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الكورس');
        });
    }
}

function toggleCourseFeatured(courseId) {
    if (confirm('هل تريد تغيير حالة ترشيح هذا الكورس؟')) {
        fetch(`/admin/advanced-courses/${courseId}/toggle-featured`, {
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
                alert('حدث خطأ في تغيير حالة الترشيح');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الترشيح');
        });
    }
}
</script>
@endpush
@endsection
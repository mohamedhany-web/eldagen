@extends('layouts.app')

@section('title', 'تصنيفات الأسئلة')
@section('header', 'تصنيفات الأسئلة')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">تنظيم الأسئلة في تصنيفات هرمية</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.question-bank.index') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-database ml-2"></i>
                        بنك الأسئلة
                    </a>
                    <a href="{{ route('admin.question-categories.create') }}" 
                       class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة تصنيف
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- التصنيفات -->
    @if($categories->count() > 0)
        <div class="space-y-4" id="categories-container">
            @foreach($categories as $category)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700" 
                     data-category-id="{{ $category->id }}">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <!-- أيقونة السحب -->
                                <div class="cursor-move text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                
                                <!-- معلومات التصنيف -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $category->name }}</h3>
                                    <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        <span>{{ $category->academicYear->name ?? 'غير محدد' }} - {{ $category->academicSubject->name ?? 'غير محدد' }}</span>
                                        <span>{{ $category->total_questions_count }} سؤال</span>
                                        @if($category->children->count() > 0)
                                            <span>{{ $category->children->count() }} تصنيف فرعي</span>
                                        @endif
                                    </div>
                                    @if($category->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ $category->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- الإجراءات -->
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $category->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                                
                                <a href="{{ route('admin.question-categories.show', $category) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors p-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="{{ route('admin.question-bank.create', ['category_id' => $category->id]) }}" 
                                   class="text-green-600 hover:text-green-800 transition-colors p-2" title="إضافة سؤال">
                                    <i class="fas fa-plus"></i>
                                </a>
                                
                                <a href="{{ route('admin.question-categories.edit', $category) }}" 
                                   class="text-indigo-600 hover:text-indigo-800 transition-colors p-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('admin.question-categories.destroy', $category) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition-colors p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- التصنيفات الفرعية -->
                        @if($category->children->count() > 0)
                            <div class="mt-4 mr-8 space-y-2">
                                @foreach($category->children as $subCategory)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3 space-x-reverse">
                                            <i class="fas fa-folder text-gray-400"></i>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $subCategory->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $subCategory->questions_count }} سؤال</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <a href="{{ route('admin.question-categories.show', $subCategory) }}" 
                                               class="text-blue-600 hover:text-blue-800 transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.question-bank.create', ['category_id' => $subCategory->id]) }}" 
                                               class="text-green-600 hover:text-green-800 transition-colors">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-tags text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد تصنيفات</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">ابدأ بإنشاء تصنيفات لتنظيم الأسئلة</p>
            <a href="{{ route('admin.question-categories.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-plus ml-2"></i>
                إنشاء أول تصنيف
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// إعداد السحب والإفلات للتصنيفات
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('categories-container');
    if (container) {
        new Sortable(container, {
            animation: 150,
            ghostClass: 'bg-blue-50 dark:bg-blue-900',
            chosenClass: 'bg-blue-100 dark:bg-blue-800',
            onEnd: function(evt) {
                const categories = [];
                container.querySelectorAll('[data-category-id]').forEach((element, index) => {
                    categories.push({
                        id: element.dataset.categoryId,
                        order: index + 1
                    });
                });
                
                // إرسال الترتيب الجديد
                fetch('{{ route("admin.question-categories.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ categories: categories })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    location.reload();
                });
            }
        });
    }
});
</script>
@endpush
@endsection

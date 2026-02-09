@extends('layouts.app')

@section('title', 'إدارة المواد الدراسية - منصة الطارق في الرياضيات')
@section('header', 'إدارة المواد الدراسية')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4 sm:py-6 lg:py-8">
    <div class="w-full px-3 sm:px-6 lg:px-8">
        <!-- الهيدر -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg mb-4 sm:mb-6">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">إدارة المواد الدراسية</h1>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة وتنظيم المواد الدراسية في المنصة</p>
                    </div>
                    <a href="{{ route('admin.academic-subjects.create') }}"
                       class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 sm:py-2 rounded-lg font-medium transition-colors min-h-[44px] sm:min-h-0">
                        <i class="fas fa-plus"></i>
                        <span>إضافة مادة جديدة</span>
                    </a>
                </div>
            </div>

            <!-- الإحصائيات السريعة -->
            <div class="px-4 sm:px-6 py-4">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 sm:p-4">
                        <div class="flex items-center gap-2 sm:gap-0 sm:block">
                            <div class="p-1.5 sm:p-2 bg-blue-100 dark:bg-blue-800 rounded-lg shrink-0">
                                <i class="fas fa-book text-blue-600 dark:text-blue-400 text-sm sm:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-medium text-blue-600 dark:text-blue-400 truncate">إجمالي المواد</p>
                                <p class="text-lg sm:text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $subjects->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 sm:p-4">
                        <div class="flex items-center gap-2 sm:gap-0 sm:block">
                            <div class="p-1.5 sm:p-2 bg-green-100 dark:bg-green-800 rounded-lg shrink-0">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-sm sm:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-medium text-green-600 dark:text-green-400 truncate">المواد النشطة</p>
                                <p class="text-lg sm:text-2xl font-bold text-green-900 dark:text-green-100">{{ $subjects->where('is_active', true)->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3 sm:p-4">
                        <div class="flex items-center gap-2 sm:gap-0 sm:block">
                            <div class="p-1.5 sm:p-2 bg-purple-100 dark:bg-purple-800 rounded-lg shrink-0">
                                <i class="fas fa-graduation-cap text-purple-600 dark:text-purple-400 text-sm sm:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-medium text-purple-600 dark:text-purple-400 truncate">إجمالي الكورسات</p>
                                <p class="text-lg sm:text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $subjects->sum('advanced_courses_count') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3 sm:p-4">
                        <div class="flex items-center gap-2 sm:gap-0 sm:block">
                            <div class="p-1.5 sm:p-2 bg-orange-100 dark:bg-orange-800 rounded-lg shrink-0">
                                <i class="fas fa-calendar-alt text-orange-600 dark:text-orange-400 text-sm sm:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs sm:text-sm font-medium text-orange-600 dark:text-orange-400 truncate">السنوات الدراسية</p>
                                <p class="text-lg sm:text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $subjects->unique('academic_year_id')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- فلاتر البحث -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg mb-4 sm:mb-6 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">البحث</label>
                    <input type="text" id="search" placeholder="ابحث عن مادة..."
                           class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-base sm:text-sm">
                </div>
                <div>
                    <label for="year_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">السنة الدراسية</label>
                    <select id="year_filter" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-base sm:text-sm">
                        <option value="">جميع السنوات</option>
                        @foreach($subjects->unique('academicYear.name')->pluck('academicYear')->filter() as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة</label>
                    <select id="status_filter" class="w-full px-3 py-2.5 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-base sm:text-sm">
                        <option value="">جميع الحالات</option>
                        <option value="active">نشط</option>
                        <option value="inactive">معطل</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- قائمة المواد الدراسية -->
        @if($subjects->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6" id="subjects-grid">
                @foreach($subjects as $subject)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 subject-card"
                     data-year="{{ $subject->academicYear ? $subject->academicYear->id : '' }}"
                     data-status="{{ $subject->is_active ? 'active' : 'inactive' }}"
                     data-name="{{ strtolower($subject->name) }}">

                    <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-start justify-between gap-2 mb-3 sm:mb-4">
                            <div class="flex items-center min-w-0">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center text-white text-lg sm:text-xl shrink-0"
                                     style="background-color: {{ $subject->color ?? '#3B82F6' }}">
                                    <i class="{{ $subject->icon ?? 'fas fa-book' }}"></i>
                                </div>
                                <div class="mr-2 sm:mr-3 min-w-0">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white truncate">{{ $subject->name }}</h3>
                                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">{{ $subject->code ?? '—' }}</p>
                                </div>
                            </div>
                            @if($subject->is_active)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full shrink-0">نشط</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full shrink-0">معطل</span>
                            @endif
                        </div>

                        @if($subject->academicYear)
                        <div class="flex items-center mb-2 sm:mb-3 text-sm">
                            <i class="fas fa-calendar-alt text-blue-500 ml-2 shrink-0"></i>
                            <span class="text-gray-600 dark:text-gray-400 truncate">{{ $subject->academicYear->name }}</span>
                        </div>
                        @endif

                        @if($subject->description)
                            <p class="text-gray-600 dark:text-gray-300 text-xs sm:text-sm mb-3 sm:mb-4 line-clamp-2">{{ Str::limit($subject->description, 100) }}</p>
                        @endif

                        <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-3 sm:mb-4">
                            <div class="text-center py-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <div class="text-xl sm:text-2xl font-bold text-blue-600">{{ $subject->advanced_courses_count ?? 0 }}</div>
                                <div class="text-xs text-gray-500">كورس</div>
                            </div>
                            <div class="text-center py-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <div class="text-xl sm:text-2xl font-bold text-green-600">{{ $subject->order ?? 0 }}</div>
                                <div class="text-xs text-gray-500">ترتيب</div>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات - متجاوبة للهاتف -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 sm:pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.academic-subjects.show', $subject) }}"
                                   class="inline-flex items-center justify-center gap-1 text-blue-600 hover:text-blue-800 text-sm font-medium py-2.5 px-3 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 min-h-[44px] sm:min-h-0 sm:py-0 sm:px-0 sm:hover:bg-transparent">
                                    <i class="fas fa-eye"></i>
                                    عرض
                                </a>
                                <a href="{{ route('admin.academic-subjects.edit', $subject) }}"
                                   class="inline-flex items-center justify-center gap-1 text-green-600 hover:text-green-800 text-sm font-medium py-2.5 px-3 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 min-h-[44px] sm:min-h-0 sm:py-0 sm:px-0 sm:hover:bg-transparent">
                                    <i class="fas fa-edit"></i>
                                    تعديل
                                </a>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="toggleStatus({{ $subject->id }})"
                                        class="inline-flex items-center justify-center gap-1 text-sm font-medium py-2.5 px-3 rounded-lg min-h-[44px] sm:min-h-0 sm:py-0 sm:px-0
                                        {{ $subject->is_active ? 'text-red-600 hover:text-red-800 hover:bg-red-50 dark:hover:bg-red-900/20' : 'text-green-600 hover:text-green-800 hover:bg-green-50 dark:hover:bg-green-900/20' }}">
                                    <i class="fas fa-toggle-{{ $subject->is_active ? 'off' : 'on' }}"></i>
                                    {{ $subject->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}
                                </button>
                                @if($subject->advanced_courses_count == 0)
                                <button type="button" onclick="deleteSubject({{ $subject->id }})"
                                        class="inline-flex items-center justify-center gap-1 text-red-600 hover:text-red-800 text-sm font-medium py-2.5 px-3 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 min-h-[44px] sm:min-h-0 sm:py-0 sm:px-0 sm:hover:bg-transparent">
                                    <i class="fas fa-trash"></i>
                                    حذف
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 sm:p-8 text-center">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book text-xl sm:text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white mb-2">لا توجد مواد دراسية</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">ابدأ بإضافة المواد الدراسية لتنظيم المحتوى التعليمي</p>
                <a href="{{ route('admin.academic-subjects.create') }}"
                   class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors min-h-[48px]">
                    <i class="fas fa-plus"></i>
                    إضافة أول مادة دراسية
                </a>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const yearFilter = document.getElementById('year_filter');
    const statusFilter = document.getElementById('status_filter');
    const subjectCards = document.querySelectorAll('.subject-card');

    function filterSubjects() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedYear = yearFilter.value;
        const selectedStatus = statusFilter.value;

        subjectCards.forEach(card => {
            const name = card.dataset.name || '';
            const year = card.dataset.year || '';
            const status = card.dataset.status || '';
            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesYear = !selectedYear || year === selectedYear;
            const matchesStatus = !selectedStatus || status === selectedStatus;
            card.style.display = (matchesSearch && matchesYear && matchesStatus) ? 'block' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterSubjects);
    if (yearFilter) yearFilter.addEventListener('change', filterSubjects);
    if (statusFilter) statusFilter.addEventListener('change', filterSubjects);
});

function toggleStatus(subjectId) {
    if (!confirm('هل أنت متأكد من تغيير حالة المادة؟')) return;
    fetch('/admin/academic-subjects/' + subjectId + '/toggle-status', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) location.reload();
        else alert('حدث خطأ أثناء تغيير الحالة');
    })
    .catch(function() { alert('حدث خطأ أثناء تغيير الحالة'); });
}

function deleteSubject(subjectId) {
    if (!confirm('هل أنت متأكد من حذف هذه المادة؟ لا يمكن التراجع عن هذا الإجراء.')) return;
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/academic-subjects/' + subjectId;
    var csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'DELETE';
    form.appendChild(csrf);
    form.appendChild(method);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection

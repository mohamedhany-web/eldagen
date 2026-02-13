@extends('layouts.app')

@section('title', 'إدارة تسجيل الطلاب')
@section('header', 'إدارة تسجيل الطلاب')

@section('content')
<div class="space-y-6">
    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- إجمالي التسجيلات -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">إجمالي التسجيلات</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-blue-600 dark:text-blue-400">جميع تسجيلات الطلاب</span>
            </div>
        </div>

        <!-- في الانتظار -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">في الانتظار</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['pending']) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-yellow-600 dark:text-yellow-400">بحاجة للتفعيل</span>
            </div>
        </div>

        <!-- نشط -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">نشط</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['active']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-green-600 dark:text-green-400">مفعل ويتعلم</span>
            </div>
        </div>

        <!-- مكتمل -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">مكتمل</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['completed']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-purple-600 dark:text-purple-400">أنهى الكورس</span>
            </div>
        </div>
    </div>

    <!-- البحث والفلترة -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">البحث والفلترة</h3>
            <a href="{{ route('admin.enrollments.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                تسجيل طالب جديد
            </a>
        </div>
        
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="البحث بالاسم أو رقم الهاتف..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                </select>
            </div>

            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الكورس</label>
                <select name="course_id" id="course_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">جميع الكورسات</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }} — {{ $course->academicSubject?->name ?? '—' }} ({{ $course->academicYear?->name ?? '—' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 items-end">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fas fa-search mr-2"></i>
                    بحث
                </button>
                <a href="{{ route('admin.enrollments.index') }}" class="btn-secondary">
                    <i class="fas fa-refresh"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- البحث السريع بالهاتف -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">البحث السريع بالهاتف</h3>
        <div class="flex gap-4">
            <div class="flex-1">
                <input type="text" id="quickSearchPhone" placeholder="أدخل رقم هاتف الطالب أو ولي الأمر..."
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            <button type="button" onclick="quickSearchByPhone()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>
                بحث سريع
            </button>
        </div>
        <div id="quickSearchResult" class="mt-4 hidden">
            <!-- نتائج البحث السريع ستظهر هنا -->
        </div>
    </div>

    <!-- قائمة التسجيلات -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($enrollments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الطالب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الكورس</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">التقدم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">تاريخ التسجيل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $enrollment->student?->name ?? '—' }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $enrollment->student?->phone ?? '—' }}</div>
                                        @if($enrollment->student?->parent_phone)
                                            <div class="text-xs text-gray-400 dark:text-gray-500">ولي الأمر: {{ $enrollment->student->parent_phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $enrollment->course?->title ?? 'كورس محذوف' }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $enrollment->course?->academicYear?->name ?? 'غير محدد' }} - 
                                    {{ $enrollment->course?->academicSubject?->name ?? 'غير محدد' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $enrollment->status_color == 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                    {{ $enrollment->status_color == 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                                    {{ $enrollment->status_color == 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                    {{ $enrollment->status_color == 'red' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : '' }}">
                                    {{ $enrollment->status_text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $enrollment->progress }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $enrollment->enrolled_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.enrollments.show', $enrollment) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($enrollment->status === 'pending')
                                        <form method="POST" action="{{ route('admin.enrollments.activate', $enrollment) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300" 
                                                    onclick="return confirm('هل تريد تفعيل هذا التسجيل؟')">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @elseif($enrollment->status === 'active')
                                        <form method="POST" action="{{ route('admin.enrollments.deactivate', $enrollment) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-orange-600 dark:text-orange-400 hover:text-orange-900 dark:hover:text-orange-300" 
                                                    onclick="return confirm('هل تريد إيقاف هذا التسجيل؟')"
                                                    title="إيقاف التسجيل">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    @elseif($enrollment->status === 'suspended')
                                        <form method="POST" action="{{ route('admin.enrollments.activate', $enrollment) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
                                                    onclick="return confirm('هل تريد إعادة تفعيل تسجيل هذا الطالب؟')"
                                                    title="إعادة التسجيل">
                                                <i class="fas fa-redo"></i>
                                                <span>إعادة التسجيل</span>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('admin.enrollments.destroy', $enrollment) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" 
                                                onclick="return confirm('هل تريد حذف هذا التسجيل؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $enrollments->appends(request()->query())->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 dark:text-gray-300 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">لا توجد تسجيلات</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">لم يتم العثور على تسجيلات تطابق معايير البحث</p>
                <a href="{{ route('admin.enrollments.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    إضافة أول تسجيل
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function quickSearchByPhone() {
    const phone = document.getElementById('quickSearchPhone').value.trim();
    const resultDiv = document.getElementById('quickSearchResult');
    
    if (!phone) {
        alert('يرجى إدخال رقم الهاتف');
        return;
    }
    
    // إظهار loader
    resultDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-600"></i> جاري البحث...</div>';
    resultDiv.classList.remove('hidden');
    
    fetch(`{{ route('admin.enrollments.search-by-phone') }}?phone=${encodeURIComponent(phone)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const student = data.student;
                resultDiv.innerHTML = `
                    <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4">
                        <h4 class="font-medium text-green-900 dark:text-green-100 mb-2">تم العثور على الطالب:</h4>
                        <div class="text-sm">
                            <p><strong>الاسم:</strong> ${student.name}</p>
                            <p><strong>هاتف الطالب:</strong> ${student.phone}</p>
                            ${student.parent_phone ? `<p><strong>هاتف ولي الأمر:</strong> ${student.parent_phone}</p>` : ''}
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.enrollments.create') }}?student_id=${student.id}" 
                               class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                <i class="fas fa-plus mr-1"></i>
                                تسجيل في كورس
                            </a>
                        </div>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4">
                        <h4 class="font-medium text-red-900 dark:text-red-100">${data.error}</h4>
                    </div>
                `;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4">
                    <h4 class="font-medium text-red-900 dark:text-red-100">حدث خطأ في البحث</h4>
                </div>
            `;
        });
}

// البحث عند الضغط على Enter
document.getElementById('quickSearchPhone').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        quickSearchByPhone();
    }
});
</script>
@endsection

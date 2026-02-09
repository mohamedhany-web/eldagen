@extends('layouts.app')

@section('title', 'إضافة كورس جديد')
@section('header', 'إضافة كورس جديد')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.advanced-courses.index') }}" class="hover:text-primary-600">الكورسات</a>
                <span class="mx-2">/</span>
                <span>إضافة كورس جديد</span>
            </nav>
            <p class="text-sm text-gray-500 dark:text-gray-400">إنشاء كورس متقدم جديد في النظام الأكاديمي</p>
        </div>
        <a href="{{ route('admin.advanced-courses.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للكورسات
        </a>
    </div>

    <!-- النموذج -->
    <form action="{{ route('admin.advanced-courses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- النموذج الرئيسي -->
        <div class="xl:col-span-2 space-y-6">
            <!-- المعلومات الأساسية -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">المعلومات الأساسية</h3>
                </div>
                <div class="p-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- عنوان الكورس -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                عنوان الكورس <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="أدخل عنوان الكورس">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- السنة الدراسية -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                السنة الدراسية <span class="text-red-500">*</span>
                            </label>
                            <select name="academic_year_id" required onchange="loadSubjects()"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                <option value="">اختر السنة الدراسية</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- المادة الدراسية -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                المادة الدراسية <span class="text-red-500">*</span>
                            </label>
                            <select name="academic_subject_id" id="academic_subject_id" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                <option value="">اختر المادة الدراسية</option>
                                @foreach($academicSubjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('academic_subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} ({{ $subject->academicYear->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_subject_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- المستوى -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">مستوى الكورس</label>
                            <select name="level" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                <option value="beginner" {{ old('level', 'beginner') == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                                <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                                <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>متقدم</option>
                            </select>
                        </div>

                        <!-- السعر -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">السعر (جنيه مصري)</label>
                            <input type="number" name="price" value="{{ old('price', 0) }}" min="0" step="0.01"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="0">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">اتركه 0 إذا كان الكورس مجاني</p>
                        </div>
                    </div>

                    <!-- الوصف -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">وصف الكورس</label>
                        <textarea name="description" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="اكتب وصفاً مفصلاً للكورس...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- التفاصيل المتقدمة -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">التفاصيل المتقدمة</h3>
                </div>
                <div class="p-6 space-y-6">
                    <!-- أهداف الكورس -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">أهداف الكورس</label>
                        <textarea name="objectives" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="ما هي الأهداف التي سيحققها الطالب من هذا الكورس؟">{{ old('objectives') }}</textarea>
                    </div>

                    <!-- المدة -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">مدة الكورس (ساعة)</label>
                        <input type="number" name="duration_hours" value="{{ old('duration_hours') }}" min="0"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                               placeholder="عدد الساعات المتوقعة للكورس">
                    </div>
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- الإعدادات -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">إعدادات الكورس</h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- تفعيل الكورس -->
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">تفعيل الكورس</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} 
                                   class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="mr-2 text-sm text-gray-700 dark:text-gray-300">نشط</span>
                        </label>
                    </div>

                    <!-- ترشيح الكورس -->
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">كورس مميز</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} 
                                   class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="mr-2 text-sm text-gray-700 dark:text-gray-300">مرشح</span>
                        </label>
                    </div>

                    <!-- وضع الدروس -->
                    <div>
                        <label for="lesson_access_mode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">وضع الدروس</label>
                        <select name="lesson_access_mode" id="lesson_access_mode" onchange="toggleRequiredPercentCreate(this)"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="strict" {{ old('lesson_access_mode', 'strict') == 'strict' ? 'selected' : '' }}>تسلسلي (Strict) — إكمال الدرس قبل التالي</option>
                            <option value="flexible" {{ old('lesson_access_mode') == 'flexible' ? 'selected' : '' }}>حر (Flexible) — الطالب يشاهد أي درس</option>
                        </select>
                    </div>
                    <div id="required_watch_percent_wrapper_create" class="{{ old('lesson_access_mode', 'strict') == 'flexible' ? 'hidden' : '' }}">
                        <label for="required_watch_percent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نسبة المشاهدة المطلوبة (%)</label>
                        <input type="number" name="required_watch_percent" id="required_watch_percent" value="{{ old('required_watch_percent', 90) }}" min="1" max="100"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- صورة الكورس -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">صورة الكورس</h3>
                </div>
                <div class="p-6">
                    <input type="file" name="thumbnail" accept="image/*" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">PNG, JPG أو JPEG - الحد الأقصى 2MB</p>
                </div>
            </div>

            <!-- تواريخ الكورس -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">تواريخ الكورس</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تاريخ البداية</label>
                        <input type="date" name="starts_at" value="{{ old('starts_at') }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تاريخ النهاية</label>
                        <input type="date" name="ends_at" value="{{ old('ends_at') }}"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- أزرار الحفظ -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="space-y-3">
                        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                            <i class="fas fa-save ml-2"></i>
                            حفظ الكورس
                        </button>
                        
                        <a href="{{ route('admin.advanced-courses.index') }}" 
                           class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-lg font-medium transition-colors text-center block">
                            إلغاء
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

@push('scripts')
<script>
function toggleRequiredPercentCreate(selectEl) {
    var wrapper = document.getElementById('required_watch_percent_wrapper_create');
    if (selectEl.value === 'strict') {
        wrapper.classList.remove('hidden');
    } else {
        wrapper.classList.add('hidden');
    }
}
function loadSubjects() {
    const yearId = document.querySelector('[name="academic_year_id"]').value;
    const subjectSelect = document.getElementById('academic_subject_id');
    
    // مسح الخيارات الحالية
    subjectSelect.innerHTML = '<option value="">اختر المادة الدراسية</option>';
    
    if (yearId) {
        fetch(`/admin/get-subjects-by-year?academic_year_id=${yearId}`)
            .then(response => response.json())
            .then(subjects => {
                subjects.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.id;
                    option.textContent = subject.name;
                    if ('{{ old("academic_subject_id") }}' == subject.id) {
                        option.selected = true;
                    }
                    subjectSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading subjects:', error);
            });
    }
}

// تحميل المواد عند تحميل الصفحة إذا كان هناك سنة محددة
document.addEventListener('DOMContentLoaded', function() {
    const yearId = document.querySelector('[name="academic_year_id"]').value;
    if (yearId) {
        loadSubjects();
    }
});
</script>
@endpush
@endsection
@extends('layouts.app')

@section('title', 'تعديل الكورس')
@section('header', 'تعديل الكورس')

@section('content')
<div class="space-y-6">
    <!-- معلومات الكورس -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">تعديل الكورس</h3>
                <a href="{{ route('admin.advanced-courses.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-arrow-right mr-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.advanced-courses.update', $advancedCourse) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- عنوان الكورس -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        عنوان الكورس <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title', $advancedCourse->title) }}" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="أدخل عنوان الكورس">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- السنة الدراسية -->
                <div>
                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        السنة الدراسية <span class="text-red-500">*</span>
                    </label>
                    <select name="academic_year_id" id="academic_year_id" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">اختر السنة الدراسية</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id', $advancedCourse->academic_year_id) == $year->id ? 'selected' : '' }}>
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
                    <label for="academic_subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        المادة الدراسية <span class="text-red-500">*</span>
                    </label>
                    <select name="academic_subject_id" id="academic_subject_id" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">اختر المادة الدراسية</option>
                        @foreach($academicSubjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('academic_subject_id', $advancedCourse->academic_subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_subject_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- المستوى -->
                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        المستوى <span class="text-red-500">*</span>
                    </label>
                    <select name="level" id="level" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">اختر المستوى</option>
                        <option value="beginner" {{ old('level', $advancedCourse->level) == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                        <option value="intermediate" {{ old('level', $advancedCourse->level) == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                        <option value="advanced" {{ old('level', $advancedCourse->level) == 'advanced' ? 'selected' : '' }}>متقدم</option>
                    </select>
                    @error('level')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- السعر -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        السعر (جنيه مصري)
                    </label>
                    <input type="number" name="price" id="price" value="{{ old('price', $advancedCourse->price) }}" min="0" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="0.00 (اتركه فارغاً للكورس المجاني)">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- المدة بالساعات -->
                <div>
                    <label for="duration_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        المدة (ساعة)
                    </label>
                    <input type="number" name="duration_hours" id="duration_hours" value="{{ old('duration_hours', $advancedCourse->duration_hours) }}" min="1"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="مثال: 40">
                    @error('duration_hours')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- وضع الدروس -->
                <div class="md:col-span-2">
                    <label for="lesson_access_mode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        وضع الدروس
                    </label>
                    <select name="lesson_access_mode" id="lesson_access_mode" onchange="toggleRequiredPercent(this)"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="strict" {{ old('lesson_access_mode', $advancedCourse->lesson_access_mode ?? 'strict') == 'strict' ? 'selected' : '' }}>تسلسلي (Strict) — يجب إكمال الدرس أو نسبة محددة قبل التالي</option>
                        <option value="flexible" {{ old('lesson_access_mode', $advancedCourse->lesson_access_mode ?? 'strict') == 'flexible' ? 'selected' : '' }}>حر (Flexible) — الطالب يشاهد أي درس متى يشاء</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">في الوضع التسلسلي لا يُفتح الدرس التالي إلا بعد إكمال السابق (أو الوصول للنسبة المحددة).</p>
                </div>

                <!-- نسبة المشاهدة المطلوبة (تظهر عند التسلسلي) -->
                <div id="required_watch_percent_wrapper" class="{{ old('lesson_access_mode', $advancedCourse->lesson_access_mode ?? 'strict') == 'flexible' ? 'hidden' : '' }}">
                    <label for="required_watch_percent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        نسبة المشاهدة المطلوبة (%) لاعتبار الدرس مكتملاً
                    </label>
                    <input type="number" name="required_watch_percent" id="required_watch_percent" value="{{ old('required_watch_percent', $advancedCourse->required_watch_percent ?? 90) }}" min="1" max="100"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="90">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">مثال: 100 = مشاهدة كاملة، 90 = 90% من الفيديو.</p>
                </div>

                <!-- الحد الأقصى للطلاب -->
                <div>
                    <label for="max_students" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الحد الأقصى للطلاب
                    </label>
                    <input type="number" name="max_students" id="max_students" value="{{ old('max_students', $advancedCourse->max_students) }}" min="1"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="مثال: 50 (اتركه فارغاً لعدم التحديد)">
                    @error('max_students')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- الوصف -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الوصف
                </label>
                <textarea name="description" id="description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                          placeholder="وصف مفصل للكورس">{{ old('description', $advancedCourse->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- أهداف الكورس -->
            <div class="mt-6">
                <label for="objectives" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    أهداف الكورس
                </label>
                <textarea name="objectives" id="objectives" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                          placeholder="الأهداف التعليمية للكورس">{{ old('objectives', $advancedCourse->objectives) }}</textarea>
                @error('objectives')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- صورة الكورس -->
            <div class="mt-6">
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    صورة الكورس
                </label>
                @if($advancedCourse->thumbnail)
                    <div class="mb-3">
                        <img src="{{ storage_url($advancedCourse->thumbnail) }}" alt="صورة الكورس الحالية" 
                             class="w-32 h-24 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">الصورة الحالية</p>
                    </div>
                @endif
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">اختر صورة جديدة لتغيير الصورة الحالية (JPG, PNG - الحد الأقصى 2MB)</p>
                @error('thumbnail')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- تواريخ الكورس -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        تاريخ البداية
                    </label>
                    <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at', $advancedCourse->starts_at ? $advancedCourse->starts_at->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('starts_at')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ends_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        تاريخ النهاية
                    </label>
                    <input type="date" name="ends_at" id="ends_at" value="{{ old('ends_at', $advancedCourse->ends_at ? $advancedCourse->ends_at->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('ends_at')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- إعدادات الكورس -->
            <div class="mt-6 space-y-4">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">إعدادات الكورس</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- حالة النشاط -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                               {{ old('is_active', $advancedCourse->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                        <label for="is_active" class="mr-2 block text-sm text-gray-700 dark:text-gray-300">
                            الكورس نشط ومتاح للتسجيل
                        </label>
                    </div>

                    <!-- كورس مميز -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" 
                               {{ old('is_featured', $advancedCourse->is_featured) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                        <label for="is_featured" class="mr-2 block text-sm text-gray-700 dark:text-gray-300">
                            كورس مميز (يظهر في المقدمة)
                        </label>
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.advanced-courses.show', $advancedCourse) }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <i class="fas fa-eye mr-2"></i>
                        عرض الكورس
                    </a>
                    <a href="{{ route('admin.advanced-courses.index') }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        حفظ التعديلات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRequiredPercent(selectEl) {
    var wrapper = document.getElementById('required_watch_percent_wrapper');
    if (selectEl.value === 'strict') {
        wrapper.classList.remove('hidden');
    } else {
        wrapper.classList.add('hidden');
    }
}
// تفعيل العلاقة بين السنة الدراسية والمواد
document.getElementById('academic_year_id').addEventListener('change', function() {
    const yearId = this.value;
    const subjectSelect = document.getElementById('academic_subject_id');
    
    // مسح الخيارات الحالية
    subjectSelect.innerHTML = '<option value="">اختر المادة الدراسية</option>';
    
    if (yearId) {
        // جلب المواد الخاصة بالسنة المختارة
        fetch(`/admin/get-subjects-by-year?academic_year_id=${yearId}`)
            .then(response => response.json())
            .then(subjects => {
                subjects.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.id;
                    option.textContent = subject.name;
                    if ({{ $advancedCourse->academic_subject_id ?? 'null' }} == subject.id) {
                        option.selected = true;
                    }
                    subjectSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
    }
});

// تشغيل التحديث عند تحميل الصفحة إذا كانت هناك سنة مختارة
document.addEventListener('DOMContentLoaded', function() {
    const yearSelect = document.getElementById('academic_year_id');
    if (yearSelect.value) {
        yearSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection

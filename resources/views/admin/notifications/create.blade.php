@extends('layouts.app')

@section('title', 'إرسال إشعار جديد')
@section('header', 'إرسال إشعار جديد')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.notifications.index') }}" class="hover:text-primary-600">الإشعارات</a>
                <span class="mx-2">/</span>
                <span>إرسال جديد</span>
            </nav>
        </div>
        <a href="{{ route('admin.notifications.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة
        </a>
    </div>

    <!-- نموذج إرسال الإشعار -->
    <form action="{{ route('admin.notifications.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- المحتوى الرئيسي -->
            <div class="xl:col-span-2 space-y-6">
                <!-- محتوى الإشعار -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">محتوى الإشعار</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- العنوان -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                عنوان الإشعار <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="مثال: إعلان مهم، تذكير بامتحان، درجات جديدة">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- النص -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                نص الإشعار <span class="text-red-500">*</span>
                            </label>
                            <textarea name="message" id="message" rows="5" required
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                      placeholder="اكتب نص الإشعار المفصل هنا...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- نوع الإشعار والأولوية -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    نوع الإشعار <span class="text-red-500">*</span>
                                </label>
                                <select name="type" id="type" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">اختر نوع الإشعار</option>
                                    @foreach($notificationTypes as $key => $type)
                                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    الأولوية <span class="text-red-500">*</span>
                                </label>
                                <select name="priority" id="priority" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">اختر الأولوية</option>
                                    @foreach($priorities as $key => $priority)
                                        <option value="{{ $key }}" {{ old('priority', 'normal') == $key ? 'selected' : '' }}>{{ $priority }}</option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- رابط الإجراء -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="action_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    رابط الإجراء (اختياري)
                                </label>
                                <input type="url" name="action_url" id="action_url" value="{{ old('action_url') }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="https://example.com/action">
                            </div>

                            <div>
                                <label for="action_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    نص زر الإجراء
                                </label>
                                <input type="text" name="action_text" id="action_text" value="{{ old('action_text') }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="مثال: عرض التفاصيل، ادخل للامتحان">
                            </div>
                        </div>

                        <!-- تاريخ انتهاء الصلاحية -->
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                تاريخ انتهاء الصلاحية (اختياري)
                            </label>
                            <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">اتركه فارغاً إذا كان الإشعار دائماً</p>
                        </div>
                    </div>
                </div>

                <!-- الاستهداف -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">استهداف المستقبلين</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- نوع الاستهداف -->
                        <div>
                            <label for="target_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                المستهدفين <span class="text-red-500">*</span>
                            </label>
                            <select name="target_type" id="target_type" required onchange="updateTargetOptions()"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                <option value="">اختر المستهدفين</option>
                                @foreach($targetTypes as $key => $type)
                                    <option value="{{ $key }}" {{ old('target_type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('target_type')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- خيارات الاستهداف -->
                        <div id="target-options" style="display: none;">
                            <!-- اختيار الكورس -->
                            <div id="course-selection" style="display: none;">
                                <label for="course_target" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اختر الكورس</label>
                                <select id="course_target" name="target_id_course"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">اختر الكورس</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }} - {{ $course->academicSubject->name ?? 'غير محدد' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- اختيار السنة الدراسية -->
                            <div id="year-selection" style="display: none;">
                                <label for="year_target" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اختر السنة الدراسية</label>
                                <select id="year_target" name="target_id_year"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">اختر السنة الدراسية</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- اختيار المادة -->
                            <div id="subject-selection" style="display: none;">
                                <label for="subject_target" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اختر المادة الدراسية</label>
                                <select id="subject_target" name="target_id_subject"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">اختر المادة</option>
                                    @foreach($academicSubjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }} - {{ $subject->academicYear->name ?? 'غير محدد' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- اختيار طالب محدد -->
                            <div id="student-selection" style="display: none;">
                                <label for="student_target" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اختر الطالب</label>
                                <select id="student_target" name="target_id_student"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">اختر الطالب</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->name }} - {{ $student->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- عدد المستهدفين -->
                        <div id="target-count-display" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4" style="display: none;">
                            <div class="flex items-center">
                                <i class="fas fa-users text-blue-600 dark:text-blue-400 ml-2"></i>
                                <span class="text-blue-800 dark:text-blue-200">سيتم إرسال الإشعار إلى: </span>
                                <span id="target-count" class="font-bold text-blue-900 dark:text-blue-100">0</span>
                                <span class="text-blue-800 dark:text-blue-200"> طالب</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معاينة الإشعار -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">معاينة الإشعار</h3>
                    </div>
                    <div class="p-6">
                        <div id="notification-preview" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 min-h-32">
                            <div class="text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-bell text-2xl mb-2"></i>
                                <p>اكتب محتوى الإشعار لرؤية المعاينة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <!-- إعدادات الإشعار -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">إعدادات الإشعار</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- تاريخ انتهاء الصلاحية -->
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                انتهاء الصلاحية
                            </label>
                            <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">اتركه فارغاً للإشعارات الدائمة</p>
                        </div>

                        <!-- خيارات الإرسال -->
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="send_immediately" value="1" 
                                       {{ old('send_immediately', true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                <span class="mr-2 text-sm text-gray-700 dark:text-gray-300">إرسال فوري</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- نصائح -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">نصائح</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-lightbulb text-blue-600 dark:text-blue-400 ml-2"></i>
                                    <span class="text-sm font-medium text-blue-800 dark:text-blue-200">نصائح للكتابة</span>
                                </div>
                                <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                                    <li>• اكتب عنواناً واضحاً ومختصراً</li>
                                    <li>• استخدم نصاً مفهوماً وودوداً</li>
                                    <li>• حدد الأولوية بعناية</li>
                                    <li>• أضف رابط إجراء إذا لزم الأمر</li>
                                </ul>
                            </div>

                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-users text-green-600 dark:text-green-400 ml-2"></i>
                                    <span class="text-sm font-medium text-green-800 dark:text-green-200">أنواع الاستهداف</span>
                                </div>
                                <ul class="text-sm text-green-700 dark:text-green-300 space-y-1">
                                    <li>• <strong>جميع الطلاب:</strong> كل الطلاب النشطين</li>
                                    <li>• <strong>كورس معين:</strong> طلاب كورس محدد</li>
                                    <li>• <strong>سنة دراسية:</strong> طلاب سنة واحدة</li>
                                    <li>• <strong>مادة معينة:</strong> طلاب مادة محددة</li>
                                    <li>• <strong>طالب محدد:</strong> طالب واحد فقط</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" 
                                    class="w-full bg-primary-600 hover:bg-primary-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                <i class="fas fa-paper-plane ml-2"></i>
                                إرسال الإشعار
                            </button>
                            
                            <a href="{{ route('admin.notifications.index') }}" 
                               class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-lg font-medium transition-colors block text-center">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- حقل مخفي للـ target_id -->
        <input type="hidden" name="target_id" id="target_id">
    </form>
</div>

@push('scripts')
<script>
function updateTargetOptions() {
    const targetType = document.getElementById('target_type').value;
    const targetOptions = document.getElementById('target-options');
    const targetCountDisplay = document.getElementById('target-count-display');
    
    // إخفاء جميع الخيارات
    document.getElementById('course-selection').style.display = 'none';
    document.getElementById('year-selection').style.display = 'none';
    document.getElementById('subject-selection').style.display = 'none';
    document.getElementById('student-selection').style.display = 'none';
    
    if (targetType) {
        targetOptions.style.display = 'block';
        targetCountDisplay.style.display = 'block';
        
        switch(targetType) {
            case 'course_students':
                document.getElementById('course-selection').style.display = 'block';
                break;
            case 'year_students':
                document.getElementById('year-selection').style.display = 'block';
                break;
            case 'subject_students':
                document.getElementById('subject-selection').style.display = 'block';
                break;
            case 'individual':
                document.getElementById('student-selection').style.display = 'block';
                break;
        }
        
        updateTargetCount();
    } else {
        targetOptions.style.display = 'none';
        targetCountDisplay.style.display = 'none';
    }
}

function updateTargetCount() {
    const targetType = document.getElementById('target_type').value;
    let targetId = null;
    
    switch(targetType) {
        case 'course_students':
            targetId = document.getElementById('course_target').value;
            document.getElementById('target_id').value = targetId;
            break;
        case 'year_students':
            targetId = document.getElementById('year_target').value;
            document.getElementById('target_id').value = targetId;
            break;
        case 'subject_students':
            targetId = document.getElementById('subject_target').value;
            document.getElementById('target_id').value = targetId;
            break;
        case 'individual':
            targetId = document.getElementById('student_target').value;
            document.getElementById('target_id').value = targetId;
            break;
        case 'all_students':
            document.getElementById('target_id').value = '';
            break;
    }
    
    if (targetType) {
        fetch(`{{ route('admin.notifications.target-count') }}?target_type=${targetType}&target_id=${targetId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('target-count').textContent = data.count;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('target-count').textContent = '0';
            });
    }
}

function updatePreview() {
    const title = document.getElementById('title').value;
    const message = document.getElementById('message').value;
    const type = document.getElementById('type').value;
    const priority = document.getElementById('priority').value;
    const actionUrl = document.getElementById('action_url').value;
    const actionText = document.getElementById('action_text').value;
    
    const preview = document.getElementById('notification-preview');
    
    if (!title && !message) {
        preview.innerHTML = `
            <div class="text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-bell text-2xl mb-2"></i>
                <p>اكتب محتوى الإشعار لرؤية المعاينة</p>
            </div>
        `;
        return;
    }
    
    let typeIcon = 'fas fa-info-circle';
    let typeColor = 'blue';
    
    const typeIcons = {
        'general': 'fas fa-info-circle',
        'course': 'fas fa-graduation-cap',
        'exam': 'fas fa-clipboard-check',
        'assignment': 'fas fa-tasks',
        'grade': 'fas fa-star',
        'announcement': 'fas fa-bullhorn',
        'reminder': 'fas fa-bell',
        'warning': 'fas fa-exclamation-triangle',
        'system': 'fas fa-cog',
    };
    
    const typeColors = {
        'general': 'blue',
        'course': 'green',
        'exam': 'purple',
        'assignment': 'orange',
        'grade': 'yellow',
        'announcement': 'red',
        'reminder': 'blue',
        'warning': 'red',
        'system': 'gray',
    };
    
    if (type) {
        typeIcon = typeIcons[type] || typeIcon;
        typeColor = typeColors[type] || typeColor;
    }
    
    let priorityBadge = '';
    if (priority && priority !== 'normal') {
        const priorityColors = {
            'low': 'gray',
            'high': 'yellow',
            'urgent': 'red'
        };
        const priorityTexts = {
            'low': 'منخفضة',
            'high': 'عالية',
            'urgent': 'عاجلة'
        };
        priorityBadge = `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-${priorityColors[priority]}-100 text-${priorityColors[priority]}-800">${priorityTexts[priority]}</span>`;
    }
    
    let actionButton = '';
    if (actionUrl && actionText) {
        actionButton = `
            <div class="mt-3">
                <a href="${actionUrl}" class="inline-flex items-center px-3 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    ${actionText}
                    <i class="fas fa-external-link-alt mr-2"></i>
                </a>
            </div>
        `;
    }
    
    preview.innerHTML = `
        <div class="flex items-start space-x-3 space-x-reverse">
            <div class="w-10 h-10 bg-${typeColor}-100 rounded-full flex items-center justify-center">
                <i class="${typeIcon} text-${typeColor}-600"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <h4 class="font-medium text-gray-900 dark:text-white">${title || 'عنوان الإشعار'}</h4>
                    ${priorityBadge}
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm">${message || 'نص الإشعار...'}</p>
                ${actionButton}
                <div class="text-xs text-gray-400 mt-2">منذ لحظات</div>
            </div>
        </div>
    `;
}

// تحديث المعاينة عند تغيير المدخلات
document.addEventListener('DOMContentLoaded', function() {
    ['title', 'message', 'type', 'priority', 'action_url', 'action_text'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updatePreview);
            field.addEventListener('change', updatePreview);
        }
    });
    
    // تحديث عداد المستهدفين عند تغيير الاختيارات
    ['course_target', 'year_target', 'subject_target', 'student_target'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', updateTargetCount);
        }
    });
    
    updatePreview();
    updateTargetOptions();
});
</script>
@endpush
@endsection

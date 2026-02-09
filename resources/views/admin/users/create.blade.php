@extends('layouts.app')

@section('title', 'إضافة مستخدم جديد - منصة الطارق في الرياضيات')
@section('header', 'إضافة مستخدم جديد')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إضافة مستخدم جديد</h1>
        <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-right"></i>
            العودة للقائمة
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-user-plus text-gray-500 dark:text-gray-400"></i>
                بيانات المستخدم
            </h3>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-6">
            @csrf

            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 pb-2 border-b border-gray-200 dark:border-gray-600">المعلومات الأساسية</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الاسم الكامل <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
                        @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">رقم الهاتف <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="01xxxxxxxx"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors" dir="ltr">
                        @error('phone')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div id="parent_phone_wrapper">
                        <label for="parent_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">جوال ولي الأمر <span id="parent_phone_required_mark" class="text-red-500 {{ old('role') === 'student' ? '' : 'hidden' }}">*</span></label>
                        <input type="text" name="parent_phone" id="parent_phone" value="{{ old('parent_phone') }}" placeholder="01xxxxxxxx" dir="ltr"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
                        @error('parent_phone')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">إلزامي عند اختيار دور طالب — يبدأ بـ 01 و 11 رقماً</p>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">كلمة المرور <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" required minlength="8"
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
                        @error('password')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">8 أحرف على الأقل</p>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 pb-2 border-b border-gray-200 dark:border-gray-600">الدور والحالة</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الدور <span class="text-red-500">*</span></label>
                        <select name="role" id="role" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
                            <option value="">اختر الدور</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>إداري</option>
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>طالب</option>
                        </select>
                        @error('role')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">حالة الحساب <span class="text-red-500">*</span></label>
                        <select name="is_active" id="is_active" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                        @error('is_active')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">وصف الأدوار:</p>
                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                        <li><span class="font-medium text-gray-700 dark:text-gray-300">إداري:</span> صلاحيات كاملة أو مخصصة (اختر أدناه)</li>
                        <li><span class="font-medium text-gray-700 dark:text-gray-300">طالب:</span> الكورسات والامتحانات</li>
                    </ul>
                </div>
            </div>

            <!-- المخصص وتحديد الصلاحيات (يظهر عند اختيار دور "إداري") -->
            <div id="permissions-section" class="{{ old('role') === 'admin' ? '' : 'hidden' }} p-5 rounded-2xl border-2 border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-900/10">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-user-shield text-indigo-600 dark:text-indigo-400 text-lg"></i>
                    <h4 class="text-base font-bold text-gray-800 dark:text-white">المخصص وتحديد الصلاحيات</h4>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">اختر الأقسام التي يراها هذا المستخدم في السايدبار فقط. إن لم تختر أي شيء = جميع الصلاحيات.</p>
                <label class="inline-flex items-center gap-2 mb-4 p-3 rounded-xl bg-white dark:bg-gray-800 border border-indigo-200 dark:border-indigo-700 cursor-pointer shadow-sm">
                    <input type="checkbox" id="permissions_all" name="permissions_all" value="1" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">جميع الصلاحيات (إظهار كل أقسام الإدارة)</span>
                </label>
                <div id="permissions-list" class="space-y-4">
                    @forelse($permissionGroups ?? [] as $groupName => $items)
                        <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800/80 shadow-sm">
                            <h5 class="text-sm font-semibold text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                                <i class="fas fa-folder-open text-indigo-500"></i>
                                {{ $groupName }}
                            </h5>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach($items as $key => $label)
                                    <label class="inline-flex items-center gap-2 p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/80 cursor-pointer border border-transparent hover:border-indigo-200 dark:hover:border-indigo-700 transition-colors">
                                        <input type="checkbox" name="permissions[]" value="{{ $key }}" class="permission-cb rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 py-2">لا توجد صلاحيات معرّفة. تحقق من إعدادات النظام.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نبذة (اختياري)</label>
                <textarea name="bio" id="bio" rows="3" placeholder="معلومات إضافية..."
                          class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-colors">{{ old('bio') }}</textarea>
                @error('bio')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.users') }}" class="px-5 py-2.5 rounded-xl font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    إلغاء
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-medium bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <i class="fas fa-save"></i>
                    إنشاء المستخدم
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('phone').addEventListener('input', function() {
    var phone = this.value.replace(/\D/g, '');
    if (phone.length > 0 && !phone.startsWith('01')) {
        this.setCustomValidity('رقم الهاتف يجب أن يبدأ بـ 01');
    } else if (phone.length > 0 && phone.length !== 11) {
        this.setCustomValidity('رقم الهاتف 11 رقماً');
    } else {
        this.setCustomValidity('');
    }
});

var roleSelect = document.getElementById('role');
var permSection = document.getElementById('permissions-section');
var permAll = document.getElementById('permissions_all');
var permissionCbs = document.querySelectorAll('.permission-cb');
var parentPhoneInput = document.getElementById('parent_phone');
var parentPhoneRequiredMark = document.getElementById('parent_phone_required_mark');

function togglePermissionsSection() {
    if (roleSelect.value === 'admin') {
        permSection.classList.remove('hidden');
    } else {
        permSection.classList.add('hidden');
    }
}
function toggleParentPhoneRequired() {
    var isStudent = roleSelect.value === 'student';
    parentPhoneInput.required = isStudent;
    if (isStudent) {
        parentPhoneRequiredMark.classList.remove('hidden');
    } else {
        parentPhoneRequiredMark.classList.add('hidden');
    }
}
roleSelect.addEventListener('change', function() {
    togglePermissionsSection();
    toggleParentPhoneRequired();
});
togglePermissionsSection();
toggleParentPhoneRequired();

permAll.addEventListener('change', function() {
    permissionCbs.forEach(function(cb) {
        cb.checked = permAll.checked;
        cb.disabled = permAll.checked;
    });
});
permissionCbs.forEach(function(cb) {
    cb.addEventListener('change', function() {
        if (!this.checked) permAll.checked = false;
    });
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'تعديل المستخدم - منصة الطارق في الرياضيات')
@section('header', 'تعديل المستخدم')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black bg-clip-text text-transparent dark:opacity-95" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; background-clip: text;">تعديل المستخدم</h1>
        <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-right"></i>
            العودة للقائمة
        </a>
    </div>

    <div class="dashboard-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300">
        <div class="px-6 py-4 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-user-edit text-xl"></i>
                بيانات المستخدم
            </h3>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">الاسم الكامل <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#667eea] focus:border-[#667eea] transition-colors">
                    @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">رقم الهاتف <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required placeholder="01xxxxxxxx"
                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#667eea] focus:border-[#667eea] transition-colors" dir="ltr">
                    @error('phone')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني (اختياري)</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#667eea] focus:border-[#667eea] transition-colors" dir="ltr">
                    @error('email')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">كلمة مرور جديدة (اتركها فارغة للإبقاء على الحالية)</label>
                    <input type="password" name="password" id="password" minlength="8"
                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#667eea] focus:border-[#667eea] transition-colors">
                    @error('password')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="role" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">الدور <span class="text-red-500">*</span></label>
                    <select name="role" id="role" required class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#667eea] focus:border-[#667eea] transition-colors">
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>إداري</option>
                        <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>مدرس</option>
                        <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>طالب</option>
                        <option value="parent" {{ old('role', $user->role) == 'parent' ? 'selected' : '' }}>ولي أمر</option>
                    </select>
                    @error('role')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="is_active" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select name="is_active" id="is_active" required class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#667eea] focus:border-[#667eea] transition-colors">
                        <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }}>غير نشط</option>
                    </select>
                    @error('is_active')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            @php
                $userPerms = old('permissions', $user->permissions ?? []);
                $hasAllPerms = $user->role === 'admin' && (empty($userPerms) || is_null($user->permissions));
            @endphp
            <div id="permissions-section" class="{{ ($user->role ?? old('role')) === 'admin' ? '' : 'hidden' }}">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 pb-2 border-b border-gray-200 dark:border-gray-600">صلاحيات مخصصة (للإداري فقط)</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">اختر الأقسام التي يراها هذا المستخدم في السايدبار. إن لم تختر أي شيء = جميع الصلاحيات.</p>
                <label class="inline-flex items-center gap-2 mb-4 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 cursor-pointer">
                    <input type="checkbox" id="permissions_all" name="permissions_all" value="1" {{ $hasAllPerms ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">جميع الصلاحيات (إظهار كل أقسام الإدارة)</span>
                </label>
                <div id="permissions-list" class="space-y-4">
                    @foreach($permissionGroups ?? [] as $groupName => $items)
                        <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700/50">
                            <h5 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">{{ $groupName }}</h5>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($items as $key => $label)
                                    <label class="inline-flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $key }}" class="permission-cb rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                                               {{ in_array($key, $userPerms) ? 'checked' : '' }} {{ $hasAllPerms ? 'disabled' : '' }}>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="bio" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">نبذة (اختياري)</label>
                <textarea name="bio" id="bio" rows="3" placeholder="معلومات إضافية..."
                          class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#667eea] focus:border-[#667eea] transition-colors">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.users') }}" class="px-5 py-2.5 rounded-xl font-semibold border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">إلغاء</a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-save"></i>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    var roleSelect = document.getElementById('role');
    var permSection = document.getElementById('permissions-section');
    var permAll = document.getElementById('permissions_all');
    var permissionCbs = document.querySelectorAll('.permission-cb');

    function togglePermissionsSection() {
        if (roleSelect && roleSelect.value === 'admin') {
            permSection.classList.remove('hidden');
        } else {
            permSection.classList.add('hidden');
        }
    }
    if (roleSelect) roleSelect.addEventListener('change', togglePermissionsSection);
    togglePermissionsSection();

    if (permAll) {
        permAll.addEventListener('change', function() {
            permissionCbs.forEach(function(cb) {
                cb.checked = permAll.checked;
                cb.disabled = permAll.checked;
            });
        });
    }
    permissionCbs.forEach(function(cb) {
        cb.addEventListener('change', function() {
            if (!this.checked && permAll) permAll.checked = false;
        });
    });
})();
</script>
@endsection

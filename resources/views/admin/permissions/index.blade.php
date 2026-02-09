@extends('layouts.app')

@section('title', 'الصلاحيات')
@section('header', 'الصلاحيات — التحكم في المستخدمين والأدوار')

@section('content')
<div class="space-y-6">
    <nav class="text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
        <span class="mx-2">/</span>
        <span>إدارة النظام</span>
        <span class="mx-2">/</span>
        <span>الصلاحيات</span>
    </nav>

    @if(session('success'))
        <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                <i class="fas fa-user-shield text-primary-500 ml-2"></i>
                التحكم في صلاحيات المستخدمين
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                تغيير دور المستخدم يحدد ما يظهر له في السايدبار: الطالب يرى كورساتي والامتحانات فقط، والأدمن يرى إدارة النظام والمحتوى.
            </p>
        </div>

        <!-- فلترة وبحث -->
        <form method="GET" action="{{ route('admin.permissions.index') }}" class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو البريد أو الجوال..."
                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm min-w-[200px]">
            <select name="role" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm">
                <option value="">كل الأدوار</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>مدير</option>
                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>طالب</option>
                <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>معلم</option>
                <option value="parent" {{ request('role') === 'parent' ? 'selected' : '' }}>ولي أمر</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-search ml-1"></i>
                بحث
            </button>
            <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition-colors">
                إعادة تعيين
            </a>
        </form>

        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-6 py-3 text-sm font-semibold">المستخدم</th>
                            <th class="px-6 py-3 text-sm font-semibold">البريد / الجوال</th>
                            <th class="px-6 py-3 text-sm font-semibold">الدور الحالي</th>
                            <th class="px-6 py-3 text-sm font-semibold">الحالة</th>
                            <th class="px-6 py-3 text-sm font-semibold">تغيير الدور / الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">#{{ $user->id }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $user->email ?? '—' }}<br>
                                    {{ $user->phone ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->role === 'admin')
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-200">مدير</span>
                                    @elseif($user->role === 'student')
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200">طالب</span>
                                    @elseif($user->role === 'teacher')
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200">معلم</span>
                                    @elseif($user->role === 'parent')
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200">ولي أمر</span>
                                    @else
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $user->role }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_active)
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200">نشط</span>
                                    @else
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200">معطل</span>
                                    @endif
                                    @if($user->isSuspended())
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200 mr-1">موقوف</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.permissions.update', $user) }}" class="flex flex-wrap items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm">
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>مدير</option>
                                            <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>طالب</option>
                                            <option value="teacher" {{ $user->role === 'teacher' ? 'selected' : '' }}>معلم</option>
                                            <option value="parent" {{ $user->role === 'parent' ? 'selected' : '' }}>ولي أمر</option>
                                        </select>
                                        <label class="inline-flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}
                                                   class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 dark:bg-gray-700">
                                            نشط
                                        </label>
                                        <button type="submit" class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors">
                                            حفظ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $users->links() }}
            </div>
        @else
            <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-users text-4xl mb-4 opacity-50"></i>
                <p class="text-lg font-medium">لا يوجد مستخدمون مطابقون للبحث</p>
                <p class="text-sm mt-1">غيّر معايير البحث أو <a href="{{ route('admin.permissions.index') }}" class="text-primary-600 hover:underline">اعرض الكل</a>.</p>
            </div>
        @endif
    </div>
</div>
@endsection

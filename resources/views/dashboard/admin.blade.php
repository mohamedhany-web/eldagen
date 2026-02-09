@extends('layouts.app')

@section('title', 'لوحة التحكم الإدارية')
@section('header', 'لوحة التحكم الإدارية')

@section('content')
<div class="space-y-8">
    <!-- عنوان رئيسي — تدرج الصفحة الرئيسية (#667eea → #764ba2) -->
    <div>
        <h1 class="text-2xl font-black bg-clip-text text-transparent dark:opacity-95" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; background-clip: text;">لوحة التحكم الإدارية</h1>
    </div>

    <!-- إحصائيات سريعة — شريط علوي ملون + أيقونة في مربع متدرج -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- إجمالي المستخدمين — أزرق كالصفحة الرئيسية -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="h-1 bg-gradient-to-r from-blue-400 to-blue-600"></div>
            <div class="p-6 flex items-start gap-4 bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-900/30 dark:to-blue-800/30 border-b border-blue-200/50 dark:border-blue-800/50">
                <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 bg-gradient-to-r from-blue-400 to-blue-600 shadow-lg">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-blue-700 dark:text-blue-300">إجمالي المستخدمين</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-0.5">{{ number_format($stats['total_users']) }}</p>
                </div>
            </div>
        </div>

        <!-- الطلاب — أخضر كالصفحة الرئيسية -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="h-1 bg-gradient-to-r from-green-400 to-green-600"></div>
            <div class="p-6 flex items-start gap-4 bg-gradient-to-br from-green-50 to-green-100/50 dark:from-green-900/30 dark:to-green-800/30 border-b border-green-200/50 dark:border-green-800/50">
                <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 bg-gradient-to-r from-green-400 to-green-600 shadow-lg">
                    <i class="fas fa-user-graduate text-white text-2xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-green-700 dark:text-green-300">الطلاب</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-0.5">{{ number_format($stats['total_students']) }}</p>
                </div>
            </div>
        </div>

        <!-- المدرسين — بنفسجي كالصفحة الرئيسية -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="h-1 bg-gradient-to-r from-purple-400 to-purple-600"></div>
            <div class="p-6 flex items-start gap-4 bg-gradient-to-br from-purple-50 to-purple-100/50 dark:from-purple-900/30 dark:to-purple-800/30 border-b border-purple-200/50 dark:border-purple-800/50">
                <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 bg-gradient-to-r from-purple-400 to-purple-600 shadow-lg">
                    <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-purple-700 dark:text-purple-300">المدرسين</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-0.5">{{ number_format($stats['total_teachers']) }}</p>
                </div>
            </div>
        </div>

        <!-- الكورسات — برتقالي كالصفحة الرئيسية -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <div class="h-1 bg-gradient-to-r from-orange-400 to-orange-600"></div>
            <div class="p-6 flex items-start gap-4 bg-gradient-to-br from-orange-50 to-orange-100/50 dark:from-orange-900/30 dark:to-orange-800/30 border-b border-orange-200/50 dark:border-orange-800/50">
                <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 bg-gradient-to-r from-orange-400 to-orange-600 shadow-lg">
                    <i class="fas fa-book text-white text-2xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-orange-700 dark:text-orange-300">الكورسات</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-0.5">{{ number_format($stats['total_courses']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- الرسوم البيانية والتحليلات -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- نشاط المستخدمين — أزرق–سيان كالصفحة الرئيسية -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300">
            <div class="px-6 py-4 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-chart-line text-xl"></i>
                        نشاط المستخدمين
                    </h3>
                    <div class="flex items-center gap-1.5">
                        <button class="px-3 py-1.5 text-sm font-semibold rounded-xl bg-white/20 text-white hover:bg-white/30 transition-colors duration-300">أسبوعي</button>
                        <button class="px-3 py-1.5 text-sm font-medium rounded-xl text-white/80 hover:bg-white/20 transition-colors duration-300">شهري</button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="h-64 bg-gray-50 dark:bg-gray-700/50 rounded-xl flex items-center justify-center border-2 border-gray-200 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <div class="text-center text-gray-500 dark:text-gray-400">
                        <div class="w-16 h-16 rounded-xl mx-auto mb-3 flex items-center justify-center bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <p class="text-sm font-medium">سيتم إضافة الرسم البياني قريباً</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- توزيع المستخدمين — أخضر–تيل كالصفحة الرئيسية -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-teal-500">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-users text-xl"></i>
                    توزيع المستخدمين
                </h3>
            </div>
            <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50/80 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-600/30">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">الطلاب</span>
                    </div>
                    <div class="text-left">
                        <p class="font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_students']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['total_users'] > 0 ? round(($stats['total_students'] / $stats['total_users']) * 100) : 0 }}%</p>
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50/80 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-600/30">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">المدرسين</span>
                    </div>
                    <div class="text-left">
                        <p class="font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_teachers']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['total_users'] > 0 ? round(($stats['total_teachers'] / $stats['total_users']) * 100) : 0 }}%</p>
                    </div>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50/80 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-600/30">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">أولياء الأمور</span>
                    </div>
                    <div class="text-left">
                        <p class="font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_parents']) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['total_users'] > 0 ? round(($stats['total_parents'] / $stats['total_users']) * 100) : 0 }}%</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-200/80 dark:border-gray-700/80">
                <a href="{{ route('admin.statistics') }}" class="text-sm font-semibold text-green-600 dark:text-green-400 hover:underline">عرض الكل</a>
            </div>
            </div>
        </div>
    </div>

    <!-- آخر المستخدمين والكورسات -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- آخر المستخدمين — تدرج المنصة كالصفحة الرئيسية -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300">
            <div class="px-6 py-4 flex items-center justify-between" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h3 class="text-lg font-bold text-white flex items-center gap-2"><i class="fas fa-users text-xl"></i>آخر المستخدمين</h3>
                <a href="{{ route('admin.users') }}" class="text-sm font-semibold text-white/90 hover:text-white hover:underline">عرض الكل</a>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    @foreach($recent_users as $user)
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors border border-transparent hover:border-gray-200/80 dark:hover:border-gray-600/50">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-semibold flex-shrink-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->phone ?? $user->email }}</p>
                        </div>
                        <div class="text-left flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium
                                @if($user->role === 'student') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                @elseif($user->role === 'teacher') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300
                                @elseif($user->role === 'parent') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                                @else bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 @endif">
                                @if($user->role === 'student') طالب
                                @elseif($user->role === 'teacher') مدرس
                                @elseif($user->role === 'parent') ولي أمر
                                @else إداري @endif
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- آخر الكورسات — أخضر–تيل كالصفحة الرئيسية -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-teal-500 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white flex items-center gap-2"><i class="fas fa-graduation-cap text-xl"></i>آخر الكورسات</h3>
                <a href="{{ route('admin.advanced-courses.index') }}" class="text-sm font-semibold text-white/90 hover:text-white hover:underline">عرض الكل</a>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    @forelse($recent_courses as $course)
                    <a href="{{ route('admin.courses.show', $course->id) }}" class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors border border-transparent hover:border-gray-200/80 dark:hover:border-gray-600/50">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-book text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $course->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $course->subject->name ?? 'غير محدد' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">بواسطة: {{ optional($course->teacher)->name ?? '-' }}</p>
                        </div>
                        <div class="text-left flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium
                                @if($course->status === 'published') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                @elseif($course->status === 'draft') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                @if($course->status === 'published') منشور
                                @elseif($course->status === 'draft') مسودة
                                @else مؤرشف @endif
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $course->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-10 text-gray-500 dark:text-gray-400">
                        <div class="w-12 h-12 rounded-2xl mx-auto mb-2 flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                            <i class="fas fa-book text-xl text-gray-400"></i>
                        </div>
                        <p class="text-sm">لا توجد كورسات بعد</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- إجراءات سريعة — ألوان الصفحة الرئيسية (أزرق، أخضر، بنفسجي، برتقالي) -->
    <div class="dashboard-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 transition-all duration-300">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-5">إجراءات سريعة</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.users') }}" class="flex flex-col items-center justify-center p-5 rounded-2xl border-2 border-gray-200 dark:border-gray-600 hover:border-blue-500/50 transition-all duration-300 group bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-900/20 dark:to-blue-800/20 hover:shadow-xl hover:-translate-y-1">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-2 transition-transform duration-300 group-hover:scale-105 bg-gradient-to-r from-blue-400 to-blue-600 shadow-lg">
                    <i class="fas fa-user-plus text-white text-xl"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">إضافة مستخدم</span>
            </a>
            <a href="{{ route('admin.advanced-courses.index') }}" class="flex flex-col items-center justify-center p-5 rounded-2xl border-2 border-gray-200 dark:border-gray-600 hover:border-green-500/50 transition-all duration-300 group bg-gradient-to-br from-green-50 to-green-100/50 dark:from-green-900/20 dark:to-green-800/20 hover:shadow-xl hover:-translate-y-1">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-r from-green-400 to-green-600 flex items-center justify-center mb-2 transition-transform duration-300 group-hover:scale-105 shadow-lg">
                    <i class="fas fa-book text-white text-xl"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-green-600 dark:group-hover:text-green-400">إضافة كورس</span>
            </a>
            <a href="{{ route('admin.academic-years.index') }}" class="flex flex-col items-center justify-center p-5 rounded-2xl border-2 border-gray-200 dark:border-gray-600 hover:border-purple-500/50 transition-all duration-300 group bg-gradient-to-br from-purple-50 to-purple-100/50 dark:from-purple-900/20 dark:to-purple-800/20 hover:shadow-xl hover:-translate-y-1">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-r from-purple-400 to-purple-600 flex items-center justify-center mb-2 transition-transform duration-300 group-hover:scale-105 shadow-lg">
                    <i class="fas fa-school text-white text-xl"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">السنوات الدراسية</span>
            </a>
            <a href="{{ route('admin.notifications.index') }}" class="flex flex-col items-center justify-center p-5 rounded-2xl border-2 border-gray-200 dark:border-gray-600 hover:border-orange-500/50 transition-all duration-300 group bg-gradient-to-br from-orange-50 to-orange-100/50 dark:from-orange-900/20 dark:to-orange-800/20 hover:shadow-xl hover:-translate-y-1">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-r from-orange-400 to-orange-600 flex items-center justify-center mb-2 transition-transform duration-300 group-hover:scale-105 shadow-lg">
                    <i class="fas fa-bell text-white text-xl"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-orange-600 dark:group-hover:text-orange-400">الإشعارات</span>
            </a>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'لوحة الإدارة - مستر طارق الداجن')
@section('page_title', 'لوحة الإدارة')

@section('content')
<div class="p-6">
    <!-- الإحصائيات الرئيسية -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- إجمالي المستخدمين -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-white text-sm"></i>
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <p class="text-sm font-medium text-gray-600">إجمالي المستخدمين</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- الطلاب -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-sm"></i>
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <p class="text-sm font-medium text-gray-600">الطلاب</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_students'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- الكورسات -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-white text-sm"></i>
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <p class="text-sm font-medium text-gray-600">الكورسات</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_courses'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- المواد الدراسية -->
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-folder text-white text-sm"></i>
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <p class="text-sm font-medium text-gray-600">المواد الدراسية</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_subjects'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- الإحصائيات الشهرية -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">المستخدمون الجدد هذا الشهر</h3>
            <div class="text-3xl font-bold text-blue-600">{{ $monthlyStats['new_users_this_month'] ?? 0 }}</div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">الامتحانات هذا الشهر</h3>
            <div class="text-3xl font-bold text-green-600">{{ $monthlyStats['exams_this_month'] ?? 0 }}</div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">التسجيلات الجديدة</h3>
            <div class="text-3xl font-bold text-purple-600">{{ $monthlyStats['course_enrollments_this_month'] ?? 0 }}</div>
        </div>
    </div>

    <!-- الأنشطة الأخيرة -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- سجل النشاطات -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">آخر النشاطات</h3>
            </div>
            <div class="p-6">
                @if(isset($stats['recent_activities']) && $stats['recent_activities']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['recent_activities']->take(5) as $activity)
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                        <i class="fas fa-history text-gray-600 text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900">
                                        {{ $activity->user->name ?? 'مستخدم محذوف' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $activity->action }} - {{ $activity->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.activity-log') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            عرض جميع النشاطات
                        </a>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">لا توجد أنشطة بعد</p>
                @endif
            </div>
        </div>

        <!-- آخر محاولات الامتحانات -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">آخر محاولات الامتحانات</h3>
            </div>
            <div class="p-6">
                @if(isset($stats['recent_exam_attempts']) && $stats['recent_exam_attempts']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['recent_exam_attempts']->take(5) as $attempt)
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $attempt->user->name ?? 'طالب محذوف' }}</p>
                                    <p class="text-sm text-gray-500">{{ $attempt->exam->title ?? 'امتحان محذوف' }}</p>
                                </div>
                                <div class="text-left">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $attempt->score >= 80 ? 'bg-green-100 text-green-800' : ($attempt->score >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $attempt->score }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">لا توجد محاولات امتحانات بعد</p>
                @endif
            </div>
        </div>
    </div>

    <!-- أزرار سريعة -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">إجراءات سريعة</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-4 rounded-lg transition-colors">
                <i class="fas fa-user-plus block mb-2"></i>
                <span class="text-sm">إضافة مستخدم</span>
            </a>
            
            <a href="{{ route('admin.academic-years.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-center py-3 px-4 rounded-lg transition-colors">
                <i class="fas fa-calendar-plus block mb-2"></i>
                <span class="text-sm">سنة دراسية</span>
            </a>
            
            <a href="{{ route('admin.academic-subjects.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white text-center py-3 px-4 rounded-lg transition-colors">
                <i class="fas fa-book-open block mb-2"></i>
                <span class="text-sm">مادة دراسية</span>
            </a>
            
            <a href="{{ route('admin.advanced-courses.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white text-center py-3 px-4 rounded-lg transition-colors">
                <i class="fas fa-graduation-cap block mb-2"></i>
                <span class="text-sm">كورس جديد</span>
            </a>
            
            <a href="{{ route('admin.orders.index') }}" class="bg-red-600 hover:bg-red-700 text-white text-center py-3 px-4 rounded-lg transition-colors">
                <i class="fas fa-shopping-cart block mb-2"></i>
                <span class="text-sm">الطلبات</span>
            </a>
            
            <a href="{{ route('admin.statistics') }}" class="bg-gray-600 hover:bg-gray-700 text-white text-center py-3 px-4 rounded-lg transition-colors">
                <i class="fas fa-chart-bar block mb-2"></i>
                <span class="text-sm">الإحصائيات</span>
            </a>
        </div>
    </div>
</div>
@endsection

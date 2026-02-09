@extends('layouts.app')

@section('title', 'لوحة تحكم الطالب')
@section('header', 'لوحة تحكم الطالب')

@section('content')
<div class="space-y-6">
    <!-- ترحيب شخصي -->
    <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">مرحباً، {{ auth()->user()->name }}</h2>
                <p class="text-green-100 mt-1">استمر في التعلم وحقق أهدافك التعليمية</p>
            </div>
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-user-graduate text-3xl"></i>
            </div>
        </div>
        
        <!-- شريط التقدم العام -->
        <div class="mt-6">
            <div class="flex items-center justify-between text-sm mb-2">
                <span>التقدم الإجمالي</span>
                <span>{{ $stats['total_progress'] }}%</span>
            </div>
            <div class="w-full bg-white bg-opacity-20 rounded-full h-2">
                <div class="bg-white h-2 rounded-full" style="width: {{ $stats['total_progress'] }}%"></div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- كورساتي المفعلة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">كورساتي المفعلة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['active_courses']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-play-circle text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('my-courses.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">عرض الكورسات</a>
            </div>
        </div>

        <!-- الكورسات المكتملة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">مكتمل</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['completed_courses']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('my-courses.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline">كورساتي</a>
            </div>
        </div>

        <!-- طلباتي المعلقة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">طلبات معلقة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['pending_orders']) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('orders.index') }}" class="text-sm text-yellow-600 dark:text-yellow-400 hover:underline">عرض الطلبات</a>
            </div>
        </div>

        <!-- التقدم الإجمالي -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">التقدم</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_progress'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-purple-600 dark:text-purple-400">متوسط التقدم</span>
            </div>
        </div>
    </div>

    <!-- كورساتي الحالية -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- الكورسات الجارية -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">كورساتي المفعلة</h3>
                    <a href="{{ route('my-courses.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">عرض الكل</a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($activeCourses as $course)
                    <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200 cursor-pointer">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-play text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $course->title }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $course->academicSubject->name ?? 'غير محدد' }} - {{ $course->academicYear->name ?? 'غير محدد' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">بواسطة: {{ $course->teacher->name ?? 'غير محدد' }}</p>
                            
                            <!-- شريط التقدم -->
                            @php
                                $progress = $course->pivot->progress ?? 0;
                            @endphp
                            <div class="mt-3">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">التقدم</span>
                                    <span class="font-medium">{{ $progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('my-courses.show', $course) }}" class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="فتح الكورس">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-book-open text-3xl mb-2"></i>
                        <p>لا توجد كورسات مفعلة</p>
                        <a href="{{ route('academic-years') }}" class="inline-flex items-center mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-search ml-2"></i>
                            استكشف الكورسات
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- الطلبات الأخيرة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">طلباتي الأخيرة</h3>
                    <a href="{{ route('orders.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">عرض الكل</a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($recentOrders as $order)
                    <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="w-10 h-10 
                            @if($order->status == 'pending') bg-yellow-100 dark:bg-yellow-900
                            @elseif($order->status == 'approved') bg-green-100 dark:bg-green-900
                            @else bg-red-100 dark:bg-red-900
                            @endif 
                            rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas 
                                @if($order->status == 'pending') fa-clock text-yellow-600 dark:text-yellow-400
                                @elseif($order->status == 'approved') fa-check text-green-600 dark:text-green-400
                                @else fa-times text-red-600 dark:text-red-400
                                @endif"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $order->course->title }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $order->course->academicSubject->name ?? 'غير محدد' }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($order->status == 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ $order->status_text }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('orders.show', $order) }}" class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-shopping-cart text-3xl mb-2"></i>
                        <p>لا توجد طلبات</p>
                        <a href="{{ route('academic-years') }}" class="inline-flex items-center mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-plus ml-2"></i>
                            اطلب كورس
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- الواجبات والاختبارات القادمة -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- الواجبات (لا يوجد نظام واجبات حالياً) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الواجبات</h3>
                    <a href="{{ route('my-courses.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">كورساتي</a>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-tasks text-3xl mb-2"></i>
                    <p class="text-sm">لا توجد واجبات معروضة حالياً</p>
                    <a href="{{ route('my-courses.index') }}" class="inline-flex items-center mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-book-open ml-2"></i>
                        الذهاب إلى كورساتي
                    </a>
                </div>
            </div>
        </div>

        <!-- الاختبارات القادمة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الاختبارات القادمة</h3>
                    <a href="{{ route('student.exams.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">عرض الكل</a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($upcomingExams as $exam)
                    <a href="{{ route('student.exams.show', $exam) }}" class="flex items-start gap-4 p-4 border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors block">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clipboard-check text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $exam->title }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $exam->course->title ?? '—' }}</p>
                            <div class="flex items-center gap-4 mt-2 flex-wrap">
                                @if($exam->end_date)
                                <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                                    <i class="fas fa-calendar ml-1"></i>
                                    حتى {{ \Carbon\Carbon::parse($exam->end_date)->translatedFormat('d M Y') }}
                                </span>
                                @endif
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    المدة: {{ $exam->duration_minutes }} دقيقة
                                </span>
                            </div>
                        </div>
                        <div class="text-left flex-shrink-0">
                            <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">عرض</span>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-clipboard-list text-3xl mb-2"></i>
                        <p class="text-sm">لا توجد اختبارات قادمة</p>
                        <a href="{{ route('student.exams.index') }}" class="inline-flex items-center mt-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                            <i class="fas fa-list ml-2"></i>
                            جميع الاختبارات
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- إنجازاتي -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">إنجازاتي</h3>
                <a href="{{ route('my-courses.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">كورساتي</a>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($stats['completed_courses'] > 0)
                <div class="flex items-center gap-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">كورسات مكتملة</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['completed_courses'] }} كورس</p>
                    </div>
                </div>
                @endif
                @if(isset($stats['passed_exams']) && $stats['passed_exams'] > 0)
                <div class="flex items-center gap-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-medal text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">امتحانات ناجحة</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['passed_exams'] }} امتحان</p>
                    </div>
                </div>
                @endif
                @if($stats['total_progress'] > 0)
                <div class="flex items-center gap-4 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">التقدم الحالي</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['total_progress'] }}%</p>
                    </div>
                </div>
                @endif
                @if($stats['completed_courses'] == 0 && (isset($stats['passed_exams']) ? $stats['passed_exams'] == 0 : true) && $stats['total_progress'] == 0)
                <div class="col-span-full text-center py-6 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-star text-3xl mb-2"></i>
                    <p class="text-sm">استمر في التعلم لترى إنجازاتك هنا</p>
                    <a href="{{ route('my-courses.index') }}" class="inline-flex items-center mt-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        <i class="fas fa-play ml-2"></i>
                        متابعة الكورسات
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

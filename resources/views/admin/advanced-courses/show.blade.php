@extends('layouts.app')

@section('title', 'تفاصيل الكورس')
@section('header', 'تفاصيل الكورس: ' . $advancedCourse->title)

@section('content')
<div class="space-y-6">
    <!-- الهيدر والعودة -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.advanced-courses.index') }}" class="hover:text-primary-600">الكورسات</a>
                <span class="mx-2">/</span>
                <span>{{ $advancedCourse->title }}</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.advanced-courses.edit', $advancedCourse) }}" 
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-edit ml-2"></i>
                تعديل
            </a>
            <a href="{{ route('admin.advanced-courses.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- معلومات أساسية وإحصائيات -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- معلومات الكورس -->
        <div class="xl:col-span-3">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">معلومات الكورس</h3>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $advancedCourse->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            {{ $advancedCourse->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                        @if($advancedCourse->is_featured)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                <i class="fas fa-star ml-1"></i>
                                مرشح
                            </span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">العنوان</label>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $advancedCourse->title }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">السنة الدراسية</label>
                                <div class="text-gray-900 dark:text-white">{{ $advancedCourse->academicYear?->name ?? 'غير محدد' }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">المادة</label>
                                <div class="text-gray-900 dark:text-white">{{ $advancedCourse->academicSubject?->name ?? 'غير محدد' }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">السعر</label>
                                <div class="text-gray-900 dark:text-white">{{ number_format($advancedCourse->price, 2) }} ج.م</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">مدة الكورس</label>
                                <div class="text-gray-900 dark:text-white">{{ $advancedCourse->duration_hours ?? 0 }} ساعة</div>
                            </div>
                        </div>
                    </div>

                    @if($advancedCourse->description)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">الوصف</label>
                            <div class="text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                {{ $advancedCourse->description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="space-y-4">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900">
                        <i class="fas fa-play-circle text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_lessons'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">دروس</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-green-100 dark:bg-green-900">
                        <i class="fas fa-users text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_students'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">طالب نشط</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-900">
                        <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending_orders'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">طلب معلق</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-900">
                        <i class="fas fa-clock text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ floor($stats['total_duration'] / 60) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">ساعة محتوى</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- تبويبات المحتوى -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700" x-data="{ activeTab: 'lessons' }">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-8 space-x-reverse px-6">
                <button @click="activeTab = 'lessons'" 
                        :class="activeTab === 'lessons' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-play-circle ml-2"></i>
                    الدروس ({{ $stats['total_lessons'] }})
                </button>
                <button @click="activeTab = 'students'" 
                        :class="activeTab === 'students' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-users ml-2"></i>
                    الطلاب ({{ $stats['total_students'] }})
                </button>
                <button @click="activeTab = 'orders'" 
                        :class="activeTab === 'orders' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-shopping-cart ml-2"></i>
                    الطلبات ({{ $advancedCourse->orders->count() }})
                </button>
                <button @click="activeTab = 'actions'" 
                        :class="activeTab === 'actions' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-cogs ml-2"></i>
                    الإجراءات
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- تبويب الدروس -->
            <div x-show="activeTab === 'lessons'">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">دروس الكورس</h4>
                    <a href="{{ route('admin.courses.lessons.create', $advancedCourse) }}" 
                       class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة درس
                    </a>
                </div>

                @if($advancedCourse->lessons->count() > 0)
                    <div class="space-y-3">
                        @foreach($advancedCourse->lessons as $lesson)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-4 space-x-reverse">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                                            @if($lesson->type == 'video') bg-blue-100 dark:bg-blue-900
                                            @elseif($lesson->type == 'document') bg-green-100 dark:bg-green-900
                                            @elseif($lesson->type == 'quiz') bg-yellow-100 dark:bg-yellow-900
                                            @else bg-purple-100 dark:bg-purple-900
                                            @endif">
                                            <i class="fas 
                                                @if($lesson->type == 'video') fa-play text-blue-600 dark:text-blue-400
                                                @elseif($lesson->type == 'document') fa-file-alt text-green-600 dark:text-green-400
                                                @elseif($lesson->type == 'quiz') fa-question-circle text-yellow-600 dark:text-yellow-400
                                                @else fa-tasks text-purple-600 dark:text-purple-400
                                                @endif"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $lesson->title }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $lesson->duration_minutes ?? 0 }} دقيقة - 
                                            @if($lesson->type == 'video') فيديو
                                            @elseif($lesson->type == 'document') مستند
                                            @elseif($lesson->type == 'quiz') كويز
                                            @else واجب
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $lesson->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $lesson->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                    <button onclick="toggleLessonStatus({{ $lesson->id }})" 
                                            class="text-gray-400 hover:text-primary-600 transition-colors">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                    <a href="{{ route('admin.courses.lessons.edit', [$advancedCourse, $lesson]) }}" 
                                       class="text-gray-400 hover:text-blue-600 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-play-circle text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">لا توجد دروس</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">ابدأ بإضافة الدروس لهذا الكورس</p>
                        <a href="{{ route('admin.courses.lessons.create', $advancedCourse) }}" 
                           class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-plus ml-2"></i>
                            إضافة أول درس
                        </a>
                    </div>
                @endif
            </div>

            <!-- تبويب الطلاب -->
            <div x-show="activeTab === 'students'" style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">الطلاب المسجلين</h4>
                    <a href="{{ route('admin.enrollments.create') }}" 
                       class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-user-plus ml-2"></i>
                        إضافة طالب
                    </a>
                </div>

                @if($advancedCourse->enrollments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الطالب</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الحالة</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">التقدم</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">تاريخ التسجيل</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($advancedCourse->enrollments as $enrollment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                                    <span class="text-primary-600 dark:text-primary-400 font-medium">
                                                        {{ $enrollment->student ? substr($enrollment->student->name, 0, 1) : '—' }}
                                                    </span>
                                                </div>
                                                <div class="mr-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $enrollment->student?->name ?? '—' }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $enrollment->student?->email ?? '—' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($enrollment->status == 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($enrollment->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @elseif($enrollment->status == 'completed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                @endif">
                                                {{ $enrollment->status_text }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                                                </div>
                                                <span class="text-sm text-gray-900 dark:text-white">{{ $enrollment->progress }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d') : 'غير محدد' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.enrollments.show', $enrollment) }}" 
                                               class="text-primary-600 hover:text-primary-900 ml-3">عرض</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">لا يوجد طلاب</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">لم يتم تسجيل أي طالب في هذا الكورس بعد</p>
                    </div>
                @endif
            </div>

            <!-- تبويب الطلبات -->
            <div x-show="activeTab === 'orders'" style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">طلبات التسجيل</h4>
                    <a href="{{ route('admin.orders.index') }}?course_id={{ $advancedCourse->id }}" 
                       class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-external-link-alt ml-2"></i>
                        عرض جميع الطلبات
                    </a>
                </div>

                @if($advancedCourse->orders->count() > 0)
                    <div class="space-y-3">
                        @foreach($advancedCourse->orders->take(10) as $order)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-4 space-x-reverse">
                                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                        <span class="text-primary-600 dark:text-primary-400 font-medium">
                                            {{ $order->user ? substr($order->user->name, 0, 1) : '—' }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->user?->name ?? '—' }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($order->status == 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @endif">
                                        {{ $order->status_text }}
                                    </span>
                                    <a href="{{ route('admin.orders.show', $order) }}" 
                                       class="text-primary-600 hover:text-primary-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-shopping-cart text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">لا توجد طلبات</h3>
                        <p class="text-gray-500 dark:text-gray-400">لا توجد طلبات تسجيل لهذا الكورس</p>
                    </div>
                @endif
            </div>

            <!-- تبويب الإجراءات -->
            <div x-show="activeTab === 'actions'" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- تفعيل/إيقاف الكورس -->
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">حالة الكورس</h5>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">تفعيل أو إيقاف الكورس للطلاب</p>
                        <button onclick="toggleCourseStatus({{ $advancedCourse->id }})" 
                                class="w-full {{ $advancedCourse->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            {{ $advancedCourse->is_active ? 'إيقاف الكورس' : 'تفعيل الكورس' }}
                        </button>
                    </div>

                    <!-- ترشيح الكورس -->
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">ترشيح الكورس</h5>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">عرض الكورس في القائمة المرشحة</p>
                        <button onclick="toggleCourseFeatured({{ $advancedCourse->id }})" 
                                class="w-full {{ $advancedCourse->is_featured ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            {{ $advancedCourse->is_featured ? 'إلغاء الترشيح' : 'ترشيح الكورس' }}
                        </button>
                    </div>

                    <!-- نسخ الكورس -->
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">نسخ الكورس</h5>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">إنشاء نسخة من الكورس والدروس</p>
                        <form action="{{ route('admin.advanced-courses.duplicate', $advancedCourse) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('هل تريد إنشاء نسخة من هذا الكورس؟')"
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                نسخ الكورس
                            </button>
                        </form>
                    </div>

                    <!-- إحصائيات متقدمة -->
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">الإحصائيات</h5>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">عرض إحصائيات مفصلة للكورس</p>
                        <a href="{{ route('admin.advanced-courses.statistics', $advancedCourse) }}" 
                           class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                            عرض الإحصائيات
                        </a>
                    </div>

                    <!-- أقسام الكورس -->
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">أقسام الكورس</h5>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">تنظيم المحتوى بأقسام (محاضرات، تمارين، إلخ)</p>
                        <a href="{{ route('admin.courses.sections.index', $advancedCourse) }}" 
                           class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                            إدارة الأقسام
                        </a>
                    </div>

                    <!-- إدارة الدروس -->
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">إدارة الدروس</h5>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">إضافة وتعديل دروس الكورس</p>
                        <a href="{{ route('admin.courses.lessons.index', $advancedCourse) }}" 
                           class="w-full bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                            إدارة الدروس
                        </a>
                    </div>

                    <!-- حذف الكورس -->
                    <div class="p-4 border border-red-200 dark:border-red-800 rounded-lg bg-red-50 dark:bg-red-900/20">
                        <h5 class="font-medium text-red-900 dark:text-red-100 mb-2">حذف الكورس</h5>
                        <p class="text-sm text-red-700 dark:text-red-300 mb-4">حذف الكورس نهائياً (لا يمكن التراجع)</p>
                        <form action="{{ route('admin.advanced-courses.destroy', $advancedCourse) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('هل أنت متأكد من حذف هذا الكورس؟ هذا الإجراء لا يمكن التراجع عنه!')"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                حذف الكورس
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleCourseStatus(courseId) {
    if (confirm('هل تريد تغيير حالة هذا الكورس؟')) {
        fetch(`/admin/advanced-courses/${courseId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ في تغيير حالة الكورس');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الكورس');
        });
    }
}

function toggleCourseFeatured(courseId) {
    if (confirm('هل تريد تغيير حالة ترشيح هذا الكورس؟')) {
        fetch(`/admin/advanced-courses/${courseId}/toggle-featured`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ في تغيير حالة الترشيح');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الترشيح');
        });
    }
}

function toggleLessonStatus(lessonId) {
    if (confirm('هل تريد تغيير حالة هذا الدرس؟')) {
        fetch(`/admin/courses/{{ $advancedCourse->id }}/lessons/${lessonId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ في تغيير حالة الدرس');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الدرس');
        });
    }
}
</script>
@endpush
@endsection
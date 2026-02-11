@extends('layouts.app')

@section('title', 'تفاصيل التسجيل')
@section('header', 'تفاصيل التسجيل')

@section('content')
<div class="space-y-6">
    <!-- الهيدر والعودة -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.enrollments.index') }}" class="hover:text-primary-600">التسجيلات</a>
                <span class="mx-2">/</span>
                <span>تفاصيل التسجيل</span>
            </nav>
        </div>
        <a href="{{ route('admin.enrollments.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- معلومات التسجيل -->
        <div class="xl:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">معلومات التسجيل</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($enrollment->status_color == 'yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @elseif($enrollment->status_color == 'green') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($enrollment->status_color == 'blue') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @elseif($enrollment->status_color == 'red') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                        @endif">
                        {{ $enrollment->status_text }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الطالب</label>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ optional($enrollment->student)->name ?? '—' }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ optional($enrollment->student)->email ?? '—' }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">رقم الهاتف</label>
                                <div class="text-gray-900 dark:text-white">{{ optional($enrollment->student)->phone ?? 'غير محدد' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">الكورس</label>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ optional($enrollment->course)->title ?? 'كورس محذوف' }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ optional(optional($enrollment->course)->academicYear)->name ?? 'غير محدد' }} -
                                    {{ optional(optional($enrollment->course)->academicSubject)->name ?? 'غير محدد' }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">التقدم</label>
                                <div class="flex items-center justify-between text-sm mb-2">
                                    <span class="text-gray-600 dark:text-gray-400">{{ $enrollment->progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-primary-600 h-3 rounded-full transition-all duration-300" 
                                         style="width: {{ $enrollment->progress }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">تاريخ التسجيل</label>
                                <div class="text-gray-900 dark:text-white">{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i') : 'غير محدد' }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">تاريخ التفعيل</label>
                                <div class="text-gray-900 dark:text-white">{{ $enrollment->activated_at ? $enrollment->activated_at->format('Y-m-d H:i') : 'غير مفعل' }}</div>
                            </div>
                        </div>
                    </div>

                    @if($enrollment->activatedBy)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">تم التفعيل بواسطة</label>
                            <div class="text-gray-900 dark:text-white">{{ $enrollment->activatedBy->name }}</div>
                        </div>
                    @endif

                    @if($enrollment->notes)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">ملاحظات</label>
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg text-gray-900 dark:text-white">{{ $enrollment->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- إجراءات سريعة -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">إجراءات سريعة</h4>
                </div>
                <div class="p-6 space-y-3">
                    @if($enrollment->status === 'pending')
                        <form action="{{ route('admin.enrollments.activate', $enrollment) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" 
                                    onclick="return confirm('هل تريد تفعيل هذا التسجيل؟')">
                                <i class="fas fa-check ml-1"></i>
                                تفعيل التسجيل
                            </button>
                        </form>
                    @elseif($enrollment->status === 'active')
                        <form action="{{ route('admin.enrollments.deactivate', $enrollment) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" 
                                    onclick="return confirm('هل تريد إلغاء تفعيل هذا التسجيل؟')">
                                <i class="fas fa-pause ml-1"></i>
                                إلغاء التفعيل
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.enrollments.index') }}" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                        <i class="fas fa-list ml-1"></i>
                        عرض جميع التسجيلات
                    </a>

                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">معلومات إضافية</h4>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">ID التسجيل</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $enrollment->id }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">تم الإنشاء</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $enrollment->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">آخر تحديث</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $enrollment->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- إحصائيات الكورس (تظهر فقط إذا كان الكورس موجوداً) -->
            @if($enrollment->course)
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">إحصائيات الكورس</h4>
                </div>
                <div class="p-6">
                    @php $course = $enrollment->course; @endphp
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $course->lessons->count() ?? 0 }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">دروس</div>
                        </div>
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $course->duration_hours ?? 0 }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">ساعة</div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 text-center">
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $course->enrollments ? $course->enrollments->where('status', 'active')->count() : 0 }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">طالب مسجل</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

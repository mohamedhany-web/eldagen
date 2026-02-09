@extends('layouts.app')

@section('title', 'عرض المادة الدراسية')
@section('header', 'عرض المادة الدراسية')

@section('content')
<div class="space-y-4 sm:space-y-6 w-full px-3 sm:px-0">
    <!-- معلومات المادة -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center text-white text-lg sm:text-xl shrink-0"
                         style="background-color: {{ $academicSubject->color ?? '#3B82F6' }}">
                        <i class="{{ $academicSubject->icon ?? 'fas fa-book' }}"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $academicSubject->name }}</h3>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ $academicSubject->academicYear->name ?? 'غير محدد' }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $academicSubject->is_active 
                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' 
                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                        {{ $academicSubject->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                    <a href="{{ route('admin.academic-subjects.edit', $academicSubject) }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors min-h-[44px] sm:min-h-0">
                        <i class="fas fa-edit"></i>
                        تعديل
                    </a>
                    <a href="{{ route('admin.academic-subjects.index') }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors min-h-[44px] sm:min-h-0">
                        <i class="fas fa-arrow-right"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <!-- معلومات أساسية -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">المعلومات الأساسية</h4>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">اسم المادة:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $academicSubject->name }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">رمز المادة:</span>
                            <span class="text-sm text-gray-900 dark:text-white font-mono">{{ $academicSubject->code }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">السنة الدراسية:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $academicSubject->academicYear->name ?? 'غير محدد' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">ترتيب العرض:</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $academicSubject->order ?? '-' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">الحالة:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $academicSubject->is_active 
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' 
                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                {{ $academicSubject->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- الوصف والتصميم -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">التصميم والوصف</h4>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">اللون:</span>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full border border-gray-300" 
                                     style="background-color: {{ $academicSubject->color ?? '#3B82F6' }}"></div>
                                <span class="text-sm text-gray-900 dark:text-white font-mono">{{ $academicSubject->color ?? '#3B82F6' }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">الأيقونة:</span>
                            <div class="flex items-center gap-2">
                                <i class="{{ $academicSubject->icon ?? 'fas fa-book' }} text-lg" 
                                   style="color: {{ $academicSubject->color ?? '#3B82F6' }}"></i>
                                <span class="text-sm text-gray-900 dark:text-white font-mono">{{ $academicSubject->icon ?? 'fas fa-book' }}</span>
                            </div>
                        </div>
                        
                        @if($academicSubject->description)
                        <div>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">الوصف:</span>
                            <p class="text-sm text-gray-900 dark:text-white mt-2 leading-relaxed">{{ $academicSubject->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات المادة -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- الكورسات -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">الكورسات</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $academicSubject->advancedCourses->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-blue-600 dark:text-blue-400">كورسات متاحة</span>
            </div>
        </div>

        <!-- الكورسات النشطة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">كورسات نشطة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $academicSubject->advancedCourses->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-green-600 dark:text-green-400">جاهزة للتسجيل</span>
            </div>
        </div>

        <!-- تاريخ الإنشاء -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">تاريخ الإنشاء</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $academicSubject->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-purple-600 dark:text-purple-400">{{ $academicSubject->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>

    <!-- الكورسات المرتبطة -->
    @if($academicSubject->advancedCourses->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الكورسات المرتبطة</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($academicSubject->advancedCourses as $course)
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $course->title }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $course->description }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $course->is_active 
                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' 
                                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                            {{ $course->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                        <a href="{{ route('admin.advanced-courses.show', $course) }}" 
                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
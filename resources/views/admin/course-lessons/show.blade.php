@extends('layouts.app')

@section('title', 'تفاصيل الدرس')
@section('header', 'تفاصيل الدرس: ' . $lesson->title)

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
                <a href="{{ route('admin.courses.lessons.index', $course) }}" class="hover:text-primary-600">دروس {{ $course->title }}</a>
                <span class="mx-2">/</span>
                <span>{{ $lesson->title }}</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.courses.lessons.edit', [$course, $lesson]) }}" 
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-edit ml-2"></i>
                تعديل الدرس
            </a>
            <a href="{{ route('admin.courses.lessons.index', $course) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- معلومات الدرس -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- المحتوى الرئيسي -->
        <div class="xl:col-span-2 space-y-6">
            <!-- معلومات أساسية -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center
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
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $lesson->title }}</h3>
                    </div>
                    
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $lesson->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            {{ $lesson->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                        @if($lesson->is_free)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                مجاني
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">نوع الدرس</label>
                            <div class="text-gray-900 dark:text-white">
                                @if($lesson->type == 'video') فيديو
                                @elseif($lesson->type == 'document') مستند
                                @elseif($lesson->type == 'quiz') كويز
                                @else واجب
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">مدة الدرس</label>
                            <div class="text-gray-900 dark:text-white">{{ $lesson->duration_minutes ?? 0 }} دقيقة</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">ترتيب الدرس</label>
                            <div class="text-gray-900 dark:text-white">{{ $lesson->order }}</div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">تاريخ الإنشاء</label>
                            <div class="text-gray-900 dark:text-white">{{ $lesson->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>

                    @if($lesson->description)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">وصف الدرس</label>
                            <div class="text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                {{ $lesson->description }}
                            </div>
                        </div>
                    @endif

                    @if($lesson->content)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">محتوى الدرس</label>
                            <div class="text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-lg whitespace-pre-wrap">{{ $lesson->content }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- محتوى الفيديو -->
            @if($lesson->type == 'video' && $lesson->video_url)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <i class="fas fa-play-circle ml-2"></i>
                                الفيديو
                            </h4>
                            <div class="flex items-center space-x-2 space-x-reverse">
                                @php
                                    $videoSource = \App\Helpers\VideoHelper::getVideoSource($lesson->video_url);
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($videoSource == 'youtube') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @elseif($videoSource == 'vimeo') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($videoSource == 'google_drive') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                    @endif">
                                    @if($videoSource == 'youtube') YouTube
                                    @elseif($videoSource == 'vimeo') Vimeo
                                    @elseif($videoSource == 'google_drive') Google Drive
                                    @elseif($videoSource == 'direct') ملف مباشر
                                    @else مصدر آخر
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="bg-black rounded-lg overflow-hidden" style="aspect-ratio: 16/9;">
                            {!! \App\Helpers\VideoHelper::generateEmbedHtml($lesson->video_url, '100%', '100%') !!}
                        </div>
                        <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                            <div>
                                <i class="fas fa-info-circle ml-1"></i>
                                المصدر: 
                                @if($videoSource == 'youtube') YouTube
                                @elseif($videoSource == 'vimeo') Vimeo
                                @elseif($videoSource == 'google_drive') Google Drive
                                @elseif($videoSource == 'direct') ملف مباشر
                                @else مصدر آخر
                                @endif
                            </div>
                            <a href="{{ $lesson->video_url }}" target="_blank" class="text-primary-600 hover:text-primary-700">
                                <i class="fas fa-external-link-alt ml-1"></i>
                                فتح في نافذة جديدة
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- المرفقات -->
            @if($lesson->attachments)
                @php
                    $attachments = json_decode($lesson->attachments, true);
                @endphp
                @if($attachments && count($attachments) > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <i class="fas fa-paperclip ml-2"></i>
                                المرفقات
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($attachments as $attachment)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3 space-x-reverse">
                                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-file text-primary-600 dark:text-primary-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $attachment['name'] }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($attachment['size'] / 1024, 2) }} KB</div>
                                            </div>
                                        </div>
                                        <a href="{{ storage_url($attachment['path'] ?? '') }}" 
                                           target="_blank"
                                           class="text-primary-600 hover:text-primary-700 font-medium">
                                            <i class="fas fa-download ml-1"></i>
                                            تحميل
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-4">
            <!-- معلومات الكورس -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h5 class="font-semibold text-gray-900 dark:text-white mb-4">معلومات الكورس</h5>
                <div class="space-y-3">
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">اسم الكورس</div>
                        <div class="text-gray-900 dark:text-white">{{ $course->title }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">السنة الدراسية</div>
                        <div class="text-gray-900 dark:text-white">{{ $course->academicYear->name ?? 'غير محدد' }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">المادة</div>
                        <div class="text-gray-900 dark:text-white">{{ $course->academicSubject->name ?? 'غير محدد' }}</div>
                    </div>
                </div>
            </div>

            <!-- إجراءات سريعة -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h5 class="font-semibold text-gray-900 dark:text-white mb-4">إجراءات سريعة</h5>
                <div class="space-y-3">
                    <button onclick="toggleLessonStatus({{ $lesson->id }})" 
                            class="w-full {{ $lesson->is_active ? 'bg-red-100 hover:bg-red-200 text-red-700' : 'bg-green-100 hover:bg-green-200 text-green-700' }} px-4 py-2 rounded-lg font-medium transition-colors">
                        {{ $lesson->is_active ? 'إيقاف الدرس' : 'تفعيل الدرس' }}
                    </button>
                    
                    <a href="{{ route('admin.courses.lessons.edit', [$course, $lesson]) }}" 
                       class="w-full bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                        تعديل الدرس
                    </a>
                    
                    <form action="{{ route('admin.courses.lessons.destroy', [$course, $lesson]) }}" 
                          method="POST" 
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الدرس؟ هذا الإجراء لا يمكن التراجع عنه!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg font-medium transition-colors">
                            حذف الدرس
                        </button>
                    </form>
                </div>
            </div>

            <!-- إحصائيات -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h5 class="font-semibold text-gray-900 dark:text-white mb-4">إحصائيات</h5>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">تاريخ الإنشاء</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $lesson->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">آخر تحديث</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $lesson->updated_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">ID الدرس</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $lesson->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleLessonStatus(lessonId) {
    if (confirm('هل تريد تغيير حالة هذا الدرس؟')) {
        fetch(`{{ route('admin.courses.lessons.index', $course) }}/${lessonId}/toggle-status`, {
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

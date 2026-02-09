@extends('layouts.app')

@section('title', 'تعديل الدرس')
@section('header', 'تعديل الدرس: ' . $lesson->title)

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
                <span>تعديل {{ $lesson->title }}</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.courses.lessons.show', [$course, $lesson]) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-eye ml-2"></i>
                عرض الدرس
            </a>
            <a href="{{ route('admin.courses.lessons.index', $course) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- معلومات الكورس -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center ml-4">
                <i class="fas fa-graduation-cap text-primary-600 dark:text-primary-400"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $course->title }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $course->academicYear->name ?? 'غير محدد' }} - {{ $course->academicSubject->name ?? 'غير محدد' }}
                </p>
            </div>
        </div>
    </div>

    <!-- نموذج تعديل الدرس -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">تعديل بيانات الدرس</h4>
        </div>

        <form action="{{ route('admin.courses.lessons.update', [$course, $lesson]) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- عنوان الدرس -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        عنوان الدرس <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $lesson->title) }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                           placeholder="أدخل عنوان الدرس"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- نوع الدرس -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        نوع الدرس <span class="text-red-500">*</span>
                    </label>
                    <select name="type" 
                            id="type" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                            required 
                            onchange="toggleTypeFields()">
                        <option value="">اختر نوع الدرس</option>
                        <option value="video" {{ old('type', $lesson->type) == 'video' ? 'selected' : '' }}>فيديو</option>
                        <option value="document" {{ old('type', $lesson->type) == 'document' ? 'selected' : '' }}>مستند</option>
                        <option value="quiz" {{ old('type', $lesson->type) == 'quiz' ? 'selected' : '' }}>كويز</option>
                        <option value="assignment" {{ old('type', $lesson->type) == 'assignment' ? 'selected' : '' }}>واجب</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- قسم الكورس -->
                <div>
                    <label for="course_section_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">القسم</label>
                    <select name="course_section_id" id="course_section_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">— بدون قسم —</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ old('course_section_id', $lesson->course_section_id) == $section->id ? 'selected' : '' }}>{{ $section->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- مدة الدرس (للفيديو تُقرأ تلقائياً) -->
                <div id="duration_minutes_wrapper">
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        مدة الدرس (بالدقائق)
                    </label>
                    <input type="number" 
                           name="duration_minutes" 
                           id="duration_minutes" 
                           value="{{ old('duration_minutes', $lesson->duration_minutes) }}"
                           min="0"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                           placeholder="{{ $lesson->type === 'video' ? 'اختياري — تُقرأ تلقائياً من الفيديو' : 'مثال: 30' }}">
                    <p id="duration_video_note" class="mt-1 text-sm text-primary-600 dark:text-primary-400 {{ $lesson->type === 'video' ? '' : 'hidden' }}">
                        <i class="fas fa-info-circle ml-1"></i>
                        للدروس من نوع فيديو: تُقرأ المدة تلقائياً من الفيديو عند أول مشاهدة من الطلاب.
                    </p>
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ترتيب الدرس -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        ترتيب الدرس
                    </label>
                    <input type="number" 
                           name="order" 
                           id="order" 
                           value="{{ old('order', $lesson->order) }}"
                           min="0"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    @error('order')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الخيارات -->
                <div class="flex items-center space-x-6 space-x-reverse">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_free" 
                               value="1"
                               {{ old('is_free', $lesson->is_free) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <span class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">درس مجاني</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $lesson->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <span class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">درس نشط</span>
                    </label>
                </div>

                <!-- تقديم الحصة: حر أم حسب النسبة -->
                <div class="md:col-span-2 flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <input type="checkbox" 
                           name="allow_flexible_submission" 
                           id="allow_flexible_submission"
                           value="1"
                           {{ old('allow_flexible_submission', $lesson->allow_flexible_submission ?? false) ? 'checked' : '' }}
                           class="mt-1 w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <div>
                        <label for="allow_flexible_submission" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">الطالب يمكنه تقديم الحصة بحرية</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">إذا فعّلت: الطالب يقدّم الحصة كما يريد (زر «تسليم الحصة»). إذا تركت معطلاً: يُعتبر الدرس مكتملاً عند الوصول لنسبة المشاهدة المطلوبة من إعدادات الكورس.</p>
                    </div>
                </div>
            </div>

            <!-- وصف الدرس -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    وصف الدرس
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4"
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                          placeholder="وصف مختصر عن محتوى الدرس">{{ old('description', $lesson->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- محتوى الدرس -->
            <div class="mt-6">
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    محتوى الدرس
                </label>
                <textarea name="content" 
                          id="content" 
                          rows="6"
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                          placeholder="محتوى الدرس التفصيلي">{{ old('content', $lesson->content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- مصدر الفيديو ورابط الفيديو (للفيديوهات) -->
            <div id="video_url_field" class="mt-6" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="video_source" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">مصدر الفيديو</label>
                        <select name="video_source" id="video_source" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="">— تخمين تلقائي من الرابط —</option>
                            <option value="youtube" {{ old('video_source', $lesson->video_source) == 'youtube' ? 'selected' : '' }}>YouTube</option>
                            <option value="vimeo" {{ old('video_source', $lesson->video_source) == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                            <option value="google_drive" {{ old('video_source', $lesson->video_source) == 'google_drive' ? 'selected' : '' }}>Google Drive</option>
                            <option value="direct" {{ old('video_source', $lesson->video_source) == 'direct' ? 'selected' : '' }}>رابط مباشر</option>
                            <option value="other" {{ old('video_source', $lesson->video_source) == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
                </div>
                <label for="video_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">رابط الفيديو</label>
                <input type="url" 
                       name="video_url" 
                       id="video_url" 
                       value="{{ old('video_url', $lesson->video_url) }}"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                       placeholder="الصق الرابط من المصدر المختار">
                
                @if($lesson->video_url)
                    <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">معاينة الفيديو الحالي:</span>
                            @php
                                $videoSourceDisplay = $lesson->video_source ?: \App\Helpers\VideoHelper::getVideoSource($lesson->video_url);
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($videoSourceDisplay == 'youtube') bg-red-100 text-red-800
                                @elseif($videoSourceDisplay == 'vimeo') bg-blue-100 text-blue-800
                                @elseif($videoSourceDisplay == 'google_drive') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($videoSourceDisplay == 'youtube') YouTube
                                @elseif($videoSourceDisplay == 'vimeo') Vimeo
                                @elseif($videoSourceDisplay == 'google_drive') Google Drive
                                @elseif($videoSourceDisplay == 'direct') ملف مباشر
                                @else مصدر آخر
                                @endif
                            </span>
                        </div>
                        <div class="bg-black rounded-lg overflow-hidden" style="aspect-ratio: 16/9; max-height: 200px;">
                            {!! \App\Helpers\VideoHelper::generateEmbedHtml($lesson->video_url, '100%', '100%', $lesson->video_source) !!}
                        </div>
                    </div>
                @endif
                
                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <p class="mb-1"><strong>المصادر المدعومة:</strong> YouTube، Vimeo، Google Drive، رابط مباشر</p>
                </div>
                @error('video_url')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- أسئلة الفيديو (عند دقيقة معينة) - تظهر للدروس من نوع فيديو فقط -->
            <div id="video_questions_section" class="mt-6" style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-question-circle ml-2"></i>
                        أسئلة الفيديو (نقاط توقف)
                    </h4>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.question-bank.create', ['return_url' => url()->current()]) }}" 
                           target="_blank"
                           class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            <i class="fas fa-plus ml-1"></i>
                            إنشاء سؤال جديد في البنك
                        </a>
                        <button type="button" id="btn-add-video-question" 
                                class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-plus ml-2"></i>
                            إضافة سؤال في الفيديو
                        </button>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    عند الوصول للثانية المحددة يتوقف الفيديو ويظهر السؤال. يمكنك اختيار ماذا يحدث عند الإجابة الخاطئة: إعادة الفيديو من البداية، أو الرجوع لنقطة السؤال السابق، أو سؤال تدريبي بدون عقوبة.
                </p>
                <div id="video-questions-list" class="space-y-3 border border-gray-200 dark:border-gray-600 rounded-lg p-4 min-h-[80px]">
                    <div id="video-questions-loading" class="text-center text-gray-500 py-4 hidden">
                        <i class="fas fa-spinner fa-spin ml-2"></i> جاري التحميل...
                    </div>
                    <div id="video-questions-empty" class="text-center text-gray-500 py-4 hidden">
                        لا توجد أسئلة في الفيديو بعد. اضغط "إضافة سؤال في الفيديو".
                    </div>
                    <!-- تُملأ ديناميكياً -->
                </div>
            </div>

            <!-- المرفقات الحالية -->
            @if($lesson->attachments)
                @php
                    $attachments = json_decode($lesson->attachments, true);
                @endphp
                @if($attachments && count($attachments) > 0)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المرفقات الحالية</label>
                        <div class="space-y-2">
                            @foreach($attachments as $index => $attachment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <i class="fas fa-file text-primary-600 dark:text-primary-400"></i>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $attachment['name'] }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format(($attachment['size'] ?? 0) / 1024, 2) }} KB</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ storage_url($attachment['path'] ?? '') }}" 
                                           target="_blank"
                                           class="text-primary-600 hover:text-primary-700 font-medium">
                                            <i class="fas fa-download ml-1"></i>
                                            تحميل
                                        </a>
                                        <form action="{{ route('admin.courses.lessons.attachments.remove', [$course, $lesson]) }}" method="POST" class="inline" onsubmit="return confirm('حذف هذا المرفق؟');">
                                            @csrf
                                            <input type="hidden" name="index" value="{{ $index }}">
                                            <button type="submit" class="text-red-600 hover:text-red-700 font-medium" title="حذف المرفق">
                                                <i class="fas fa-trash ml-1"></i>
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <!-- رفع مرفقات جديدة -->
            <div class="mt-6">
                <label for="attachments" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    إضافة مرفقات جديدة (اختياري)
                </label>
                <input type="file" 
                       name="attachments[]" 
                       id="attachments" 
                       multiple
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">يمكن رفع عدة ملفات. الحد الأقصى لكل ملف: 10 ميجابايت. سيتم إضافتها للمرفقات الحالية.</p>
                @error('attachments.*')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- أزرار الحفظ -->
            <div class="flex items-center justify-end space-x-4 space-x-reverse mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.courses.lessons.show', [$course, $lesson]) }}" 
                   class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition-colors">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-save ml-2"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

<!-- نافذة إضافة سؤال في الفيديو -->
<div id="modal-add-video-question" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-question-circle ml-2"></i>
            إضافة سؤال في الفيديو
        </h3>
        <input type="hidden" id="add-vq-question-id" value="">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الثانية التي يظهر فيها السؤال</label>
                <input type="number" id="add-vq-time-seconds" min="0" value="0" 
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">عند الإجابة الخاطئة</label>
                <select id="add-vq-on-wrong" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    @foreach(\App\Models\LessonVideoQuestion::onWrongOptions() as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">السؤال</label>
                <div class="flex items-center gap-2">
                    <span id="add-vq-question-title" class="flex-1 text-gray-500 dark:text-gray-400 text-sm">لم يتم اختيار سؤال</span>
                    <button type="button" id="btn-open-bank-picker" class="bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-3 py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-database ml-1"></i>
                        اختيار من البنك
                    </button>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-6">
            <button type="button" id="btn-cancel-add-vq" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium">إلغاء</button>
            <button type="button" id="btn-save-video-question" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium">
                <i class="fas fa-save ml-2"></i>
                حفظ
            </button>
        </div>
    </div>
</div>

<!-- نافذة اختيار السؤال من البنك -->
<div id="modal-bank-picker" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-database ml-2"></i>
                اختيار سؤال من البنك
            </h3>
            <button type="button" id="btn-close-bank-picker" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="flex flex-1 overflow-hidden min-h-0">
            <div class="w-64 border-l border-gray-200 dark:border-gray-700 overflow-y-auto p-3">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">التصنيفات</p>
                <div id="bank-categories-list" class="space-y-1">
                    <button type="button" class="bank-cat-item w-full text-right px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" data-id="">
                        الكل
                    </button>
                </div>
            </div>
            <div class="flex-1 flex flex-col overflow-hidden p-4">
                <div class="mb-3">
                    <input type="text" id="bank-search" placeholder="بحث في نص السؤال..." 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                </div>
                <div id="bank-questions-list" class="flex-1 overflow-y-auto space-y-2">
                    <!-- تُملأ ديناميكياً -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة معاينة السؤال -->
<div id="modal-question-preview" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] flex flex-col">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">معاينة السؤال</h3>
            <button type="button" id="btn-close-preview" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="question-preview-content" class="p-4 overflow-y-auto flex-1 text-right">
            <!-- يُحمّل عبر fetch -->
        </div>
    </div>
</div>

@push('scripts')
<script>
const videoQuestionsIndexUrl = '{{ route("admin.courses.lessons.video-questions.index", [$course, $lesson]) }}';
const videoQuestionsStoreUrl = '{{ route("admin.courses.lessons.video-questions.store", [$course, $lesson]) }}';
const videoQuestionsDestroyUrl = '{{ route("admin.courses.lessons.video-questions.destroy", [$course, $lesson, "__id__"]) }}';
const bankCategoriesUrl = '{{ route("admin.lesson-video-questions.bank-categories") }}';
const bankQuestionsUrl = '{{ route("admin.lesson-video-questions.bank-questions") }}';
const questionPreviewUrl = '{{ route("admin.lesson-video-questions.question-preview", ["question" => "__id__"]) }}';
const csrfToken = '{{ csrf_token() }}';

function toggleTypeFields() {
    const type = document.getElementById('type').value;
    const videoUrlField = document.getElementById('video_url_field');
    const videoQuestionsSection = document.getElementById('video_questions_section');
    const durationNote = document.getElementById('duration_video_note');
    const durationInput = document.getElementById('duration_minutes');
    
    videoUrlField.style.display = 'none';
    if (videoQuestionsSection) videoQuestionsSection.style.display = 'none';
    if (durationNote) durationNote.classList.add('hidden');
    if (durationInput) durationInput.placeholder = 'مثال: 30';
    
    if (type === 'video') {
        videoUrlField.style.display = 'block';
        if (durationNote) durationNote.classList.remove('hidden');
        if (durationInput) durationInput.placeholder = 'اختياري — تُقرأ المدة تلقائياً من الفيديو';
        if (videoQuestionsSection) {
            videoQuestionsSection.style.display = 'block';
            loadVideoQuestions();
        }
    }
}

function loadVideoQuestions() {
    const listEl = document.getElementById('video-questions-list');
    const loadingEl = document.getElementById('video-questions-loading');
    const emptyEl = document.getElementById('video-questions-empty');
    if (!listEl) return;
    [].slice.call(listEl.querySelectorAll('.video-question-row')).forEach(function(r) { r.remove(); });
    if (loadingEl) loadingEl.classList.remove('hidden');
    if (emptyEl) emptyEl.classList.add('hidden');
    fetch(videoQuestionsIndexUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (loadingEl) loadingEl.classList.add('hidden');
            var items = data.items || [];
            if (items.length === 0) {
                if (emptyEl) emptyEl.classList.remove('hidden');
                return;
            }
            items.forEach(function(item) {
                var row = document.createElement('div');
                row.className = 'video-question-row flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg';
                var title = (item.question && item.question.question) ? item.question.question.substring(0, 60) + (item.question.question.length > 60 ? '...' : '') : ('سؤال #' + item.id);
                var onWrongLabel = item.on_wrong === 'restart_video' ? 'إعادة الفيديو' : item.on_wrong === 'rewind_to_previous' ? 'من السؤال السابق' : 'تدريبي';
                row.innerHTML = '<div class="flex-1"><span class="font-mono text-primary-600 dark:text-primary-400">' + (item.time_formatted || (Math.floor(item.time_seconds/60) + ':' + String(item.time_seconds%60).padStart(2,'0'))) + '</span> — ' + title + ' <span class="text-xs text-gray-500">(' + onWrongLabel + ')</span></div>' +
                    '<button type="button" class="btn-delete-vq text-red-600 hover:text-red-700 px-2 py-1 text-sm" data-id="' + item.id + '"><i class="fas fa-trash"></i></button>';
                listEl.appendChild(row);
                row.querySelector('.btn-delete-vq').addEventListener('click', function() { deleteVideoQuestion(this.getAttribute('data-id')); });
            });
        })
        .catch(function() {
            if (loadingEl) loadingEl.classList.add('hidden');
            if (emptyEl) emptyEl.classList.remove('hidden');
        });
}

function deleteVideoQuestion(id) {
    if (!confirm('حذف هذا السؤال من الفيديو؟')) return;
    var url = videoQuestionsDestroyUrl.replace('__id__', id);
    fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(function(r) { return r.json(); })
        .then(function() { loadVideoQuestions(); })
        .catch(function() { alert('حدث خطأ'); });
}

function openAddVideoQuestionModal() {
    document.getElementById('add-vq-question-id').value = '';
    document.getElementById('add-vq-time-seconds').value = '0';
    document.getElementById('add-vq-question-title').textContent = 'لم يتم اختيار سؤال';
    document.getElementById('modal-add-video-question').classList.remove('hidden');
    document.getElementById('modal-add-video-question').classList.add('flex');
}

function closeAddVideoQuestionModal() {
    document.getElementById('modal-add-video-question').classList.add('hidden');
    document.getElementById('modal-add-video-question').classList.remove('flex');
}

function openBankPicker() {
    document.getElementById('modal-bank-picker').classList.remove('hidden');
    document.getElementById('modal-bank-picker').classList.add('flex');
    loadBankCategories();
    loadBankQuestions();
}

function closeBankPicker() {
    document.getElementById('modal-bank-picker').classList.add('hidden');
    document.getElementById('modal-bank-picker').classList.remove('flex');
}

function loadBankCategories() {
    fetch(bankCategoriesUrl, { headers: { 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(cats) {
            var cont = document.getElementById('bank-categories-list');
            cont.innerHTML = '<button type="button" class="bank-cat-item w-full text-right px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium" data-id="">الكل</button>';
            (cats || []).forEach(function(c) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'bank-cat-item w-full text-right px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
                btn.setAttribute('data-id', c.id);
                btn.textContent = c.name + (c.questions_count ? ' (' + c.questions_count + ')' : '');
                cont.appendChild(btn);
            });
            cont.querySelectorAll('.bank-cat-item').forEach(function(b) {
                b.addEventListener('click', function() {
                    cont.querySelectorAll('.bank-cat-item').forEach(function(x) { x.classList.remove('bg-primary-100', 'dark:bg-primary-900'); });
                    b.classList.add('bg-primary-100', 'dark:bg-primary-900');
                    document.getElementById('bank-search').dataset.categoryId = b.getAttribute('data-id') || '';
                    loadBankQuestions();
                });
            });
        });
}

function loadBankQuestions() {
    var catId = (document.getElementById('bank-search') || {}).dataset?.categoryId || '';
    var search = (document.getElementById('bank-search') || {}).value || '';
    var url = bankQuestionsUrl + '?category_id=' + encodeURIComponent(catId) + '&search=' + encodeURIComponent(search);
    var list = document.getElementById('bank-questions-list');
    list.innerHTML = '<p class="text-gray-500 text-sm py-4">جاري التحميل...</p>';
    fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(questions) {
            list.innerHTML = '';
            (questions || []).forEach(function(q) {
                var card = document.createElement('div');
                card.className = 'flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg';
                var title = (q.question || '').substring(0, 80) + ((q.question || '').length > 80 ? '...' : '');
                card.innerHTML = '<div class="flex-1 text-sm text-gray-800 dark:text-gray-200">' + title + '</div>' +
                    '<div class="flex items-center gap-2"><button type="button" class="btn-preview-q text-primary-600 hover:text-primary-700 px-2 py-1 text-sm" data-id="' + q.id + '"><i class="fas fa-eye ml-1"></i> معاينة</button>' +
                    '<button type="button" class="btn-select-q bg-primary-600 text-white px-3 py-1 rounded text-sm" data-id="' + q.id + '" data-title="' + title.replace(/"/g, '&quot;') + '"><i class="fas fa-check ml-1"></i> اختيار</button></div>';
                list.appendChild(card);
                card.querySelector('.btn-preview-q').addEventListener('click', function() { openQuestionPreview(this.getAttribute('data-id')); });
                card.querySelector('.btn-select-q').addEventListener('click', function() {
                    document.getElementById('add-vq-question-id').value = this.getAttribute('data-id');
                    document.getElementById('add-vq-question-title').textContent = this.getAttribute('data-title');
                    closeBankPicker();
                });
            });
            if ((questions || []).length === 0) list.innerHTML = '<p class="text-gray-500 text-sm py-4">لا توجد أسئلة</p>';
        })
        .catch(function() { list.innerHTML = '<p class="text-red-500 text-sm py-4">حدث خطأ في التحميل</p>'; });
}

function openQuestionPreview(questionId) {
    var url = questionPreviewUrl.replace('__id__', questionId);
    var content = document.getElementById('question-preview-content');
    content.innerHTML = '<p class="text-gray-500">جاري التحميل...</p>';
    document.getElementById('modal-question-preview').classList.remove('hidden');
    document.getElementById('modal-question-preview').classList.add('flex');
    fetch(url, { headers: { 'Accept': 'text/html' } })
        .then(function(r) { return r.text(); })
        .then(function(html) {
            content.innerHTML = html;
        })
        .catch(function() { content.innerHTML = '<p class="text-red-500">حدث خطأ</p>'; });
}

function closeQuestionPreview() {
    document.getElementById('modal-question-preview').classList.add('hidden');
    document.getElementById('modal-question-preview').classList.remove('flex');
}

function saveVideoQuestion() {
    var questionId = document.getElementById('add-vq-question-id').value;
    var timeSeconds = parseInt(document.getElementById('add-vq-time-seconds').value, 10) || 0;
    var onWrong = document.getElementById('add-vq-on-wrong').value;
    if (!questionId) {
        alert('يرجى اختيار سؤال من البنك أولاً.');
        return;
    }
    fetch(videoQuestionsStoreUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ time_seconds: timeSeconds, question_id: questionId, on_wrong: onWrong })
    })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                closeAddVideoQuestionModal();
                loadVideoQuestions();
            } else {
                alert(data.message || 'حدث خطأ');
            }
        })
        .catch(function() { alert('حدث خطأ في الاتصال'); });
}

document.addEventListener('DOMContentLoaded', function() {
    toggleTypeFields();
    var typeEl = document.getElementById('type');
    if (typeEl) typeEl.addEventListener('change', toggleTypeFields);

    document.getElementById('btn-add-video-question').addEventListener('click', openAddVideoQuestionModal);
    document.getElementById('btn-cancel-add-vq').addEventListener('click', closeAddVideoQuestionModal);
    document.getElementById('btn-save-video-question').addEventListener('click', saveVideoQuestion);
    document.getElementById('btn-open-bank-picker').addEventListener('click', openBankPicker);
    document.getElementById('btn-close-bank-picker').addEventListener('click', closeBankPicker);
    document.getElementById('btn-close-preview').addEventListener('click', closeQuestionPreview);
    var bankSearch = document.getElementById('bank-search');
    if (bankSearch) {
        bankSearch.dataset.categoryId = '';
        bankSearch.addEventListener('input', function() { loadBankQuestions(); });
    }
});
</script>
@endpush
@endsection

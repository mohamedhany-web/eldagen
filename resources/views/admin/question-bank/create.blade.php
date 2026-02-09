@extends('layouts.app')

@section('title', 'إضافة سؤال جديد')
@section('header', 'إضافة سؤال جديد لبنك الأسئلة')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.question-bank.index') }}" class="hover:text-primary-600">بنك الأسئلة</a>
                <span class="mx-2">/</span>
                <span>إضافة سؤال جديد</span>
            </nav>
        </div>
        <a href="{{ route('admin.question-bank.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة
        </a>
    </div>

    <!-- نموذج إضافة السؤال -->
    <form action="{{ route('admin.question-bank.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- المحتوى الرئيسي -->
            <div class="xl:col-span-2 space-y-6">
                <!-- معلومات أساسية -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">معلومات السؤال</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- نص السؤال (يُخفى عند نوع "سؤال بصورة") -->
                        <div id="question-text-wrapper">
                            <label for="question" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                نص السؤال <span class="text-red-500">*</span>
                            </label>
                            <textarea name="question" id="question" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                      placeholder="اكتب نص السؤال هنا...">{{ old('question') }}</textarea>
                            @error('question')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- تنبيه عند نوع سؤال بصورة -->
                        <div id="image-question-notice" class="hidden p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <i class="fas fa-image ml-2"></i>
                                في هذا النوع يكون <strong>السؤال نفسه عبارة عن صورة</strong>. ارفع الصورة في قسم "الوسائط المرفقة" أو أدخل رابط الصورة، ثم اكتب الخيارات أدناه.
                            </p>
                        </div>

                        <!-- نوع السؤال -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    نوع السؤال <span class="text-red-500">*</span>
                                </label>
                                <select name="type" id="type" required onchange="toggleQuestionFields()"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">اختر نوع السؤال</option>
                                    @foreach(\App\Models\Question::getQuestionTypes() as $key => $type)
                                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="difficulty_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    مستوى الصعوبة <span class="text-red-500">*</span>
                                </label>
                                <select name="difficulty_level" id="difficulty_level" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">اختر مستوى الصعوبة</option>
                                    @foreach(\App\Models\Question::getDifficultyLevels() as $key => $level)
                                        <option value="{{ $key }}" {{ old('difficulty_level') == $key ? 'selected' : '' }}>{{ $level }}</option>
                                    @endforeach
                                </select>
                                @error('difficulty_level')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- الدرجة والوقت -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="points" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    درجة السؤال <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="points" id="points" step="0.5" min="0.5" max="100" 
                                       value="{{ old('points', 1) }}" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="1.0">
                                @error('points')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="time_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    الوقت المحدد (ثانية)
                                </label>
                                <input type="number" name="time_limit" id="time_limit" min="10" max="600" 
                                       value="{{ old('time_limit') }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="اتركه فارغاً لاستخدام وقت الامتحان العام">
                                @error('time_limit')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- خيارات السؤال (تظهر حسب النوع) -->
                <div id="question-options" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700" style="display: none;">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">خيارات السؤال</h3>
                    </div>
                    <div class="p-6">
                        <!-- اختيار متعدد -->
                        <div id="multiple-choice-options" style="display: none;">
                            <div class="space-y-4">
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="correct_option" value="{{ $i }}" id="correct_{{ $i }}"
                                               class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500">
                                        <label for="option_{{ $i }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">الخيار {{ $i }}:</label>
                                        <input type="text" name="option_{{ $i }}" id="option_{{ $i }}" 
                                               value="{{ old('option_' . $i) }}"
                                               class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                               placeholder="اكتب الخيار {{ $i }} {{ $i <= 2 ? '(مطلوب للاختيار المتعدد)' : '(اختياري)' }}">
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- صح أو خطأ -->
                        <div id="true-false-options" style="display: none;">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">الإجابة الصحيحة:</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="true_false_answer" value="صح" 
                                               {{ old('true_false_answer', 'صح') == 'صح' ? 'checked' : '' }}
                                               class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500">
                                        <span class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">صح</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="true_false_answer" value="خطأ" 
                                               {{ old('true_false_answer', 'صح') == 'خطأ' ? 'checked' : '' }}
                                               class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500">
                                        <span class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">خطأ</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- املأ الفراغ -->
                        <div id="fill-blank-options" style="display: none;">
                            <div>
                                <label for="correct_answers" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    الإجابات الصحيحة (مفصولة بفواصل)
                                </label>
                                <input type="text" name="correct_answers" id="correct_answers" 
                                       value="{{ old('correct_answers') }}"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="الإجابة الأولى, الإجابة الثانية, ...">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">يمكنك إدخال عدة إجابات صحيحة مفصولة بفواصل</p>
                            </div>
                        </div>

                        <!-- إجابة قصيرة/مقالي -->
                        <div id="text-answer-options" style="display: none;">
                            <div>
                                <label for="model_answer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    الإجابة النموذجية (اختياري)
                                </label>
                                <textarea name="model_answer" id="model_answer" rows="4"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="اكتب الإجابة النموذجية للمساعدة في التصحيح...">{{ old('model_answer') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ستساعد في التصحيح اليدوي</p>
                            </div>
                        </div>

                        <!-- مطابقة -->
                        <div id="matching-options" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="left_items" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        العناصر اليسرى (كل عنصر في سطر)
                                    </label>
                                    <textarea name="left_items" id="left_items" rows="5"
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                              placeholder="العنصر الأول&#10;العنصر الثاني&#10;العنصر الثالث">{{ old('left_items') }}</textarea>
                                </div>
                                <div>
                                    <label for="right_items" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        العناصر اليمنى (كل عنصر في سطر)
                                    </label>
                                    <textarea name="right_items" id="right_items" rows="5"
                                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                              placeholder="المطابق الأول&#10;المطابق الثاني&#10;المطابق الثالث">{{ old('right_items') }}</textarea>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    المطابقات الصحيحة
                                </label>
                                <div id="matching-pairs" class="space-y-2">
                                    <!-- سيتم إنشاؤها ديناميكياً بـ JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- ترتيب -->
                        <div id="ordering-options" style="display: none;">
                            <div>
                                <label for="ordering_items" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    العناصر للترتيب (كل عنصر في سطر)
                                </label>
                                <textarea name="ordering_items" id="ordering_items" rows="5"
                                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="العنصر الأول&#10;العنصر الثاني&#10;العنصر الثالث&#10;العنصر الرابع">{{ old('ordering_items') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">اكتب العناصر بالترتيب الصحيح</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الوسائط -->
                <div id="media-section" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الوسائط المرفقة</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- رفع صورة (مطلوب لسؤال بصورة) -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                رفع صورة <span id="image-required-star" class="text-red-500 hidden">*</span>
                            </label>
                            <input type="file" name="image" id="image" accept="image/*"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">الحد الأقصى: 2 ميجابايت. الأنواع المدعومة: JPG, PNG, GIF</p>
                        </div>

                        <!-- أو رابط صورة خارجي -->
                        <div>
                            <label for="image_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                أو رابط صورة خارجي <span id="image-url-required-star" class="text-red-500 hidden">*</span>
                            </label>
                            <input type="url" name="image_url" id="image_url" 
                                   value="{{ old('image_url') }}"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="https://example.com/image.jpg">
                        </div>

                        <!-- رابط صوتي -->
                        <div>
                            <label for="audio_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                رابط ملف صوتي
                            </label>
                            <input type="url" name="audio_url" id="audio_url" 
                                   value="{{ old('audio_url') }}"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="https://example.com/audio.mp3">
                        </div>

                        <!-- رابط فيديو -->
                        <div>
                            <label for="video_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                رابط فيديو
                            </label>
                            <input type="url" name="video_url" id="video_url" 
                                   value="{{ old('video_url') }}"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="https://www.youtube.com/watch?v=... أو أي رابط فيديو">
                        </div>
                    </div>
                </div>

                <!-- الشرح -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">شرح الإجابة</h3>
                    </div>
                    <div class="p-6">
                        <textarea name="explanation" id="explanation" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="اكتب شرحاً مفصلاً للإجابة الصحيحة (اختياري)...">{{ old('explanation') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">سيظهر للطلاب بعد الانتهاء من الامتحان (حسب إعدادات الامتحان)</p>
                    </div>
                </div>
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <!-- التصنيف والتاجز -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">التصنيف والتاجز</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- التصنيف -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                التصنيف <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id" id="category_id" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                <option value="">اختر التصنيف</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ (old('category_id', $selectedCategory) == $category->id) ? 'selected' : '' }}>
                                        {{ $category->full_path }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- التاجز -->
                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                التاجز (مفصولة بفواصل)
                            </label>
                            <input type="text" name="tags" id="tags" 
                                   value="{{ old('tags') }}"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="رياضيات, جبر, معادلات">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ستساعد في البحث والتصنيف</p>
                        </div>

                        <!-- حالة السؤال -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                                <span class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">سؤال نشط</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- معاينة -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">معاينة السؤال</h3>
                    </div>
                    <div class="p-6">
                        <div id="question-preview" class="min-h-32 bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center text-gray-500 dark:text-gray-400">
                            اكتب السؤال لرؤية المعاينة
                        </div>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" 
                                    class="w-full bg-primary-600 hover:bg-primary-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                <i class="fas fa-save ml-2"></i>
                                حفظ السؤال
                            </button>
                            
                            <button type="submit" name="save_and_new" value="1"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition-colors">
                                <i class="fas fa-plus ml-2"></i>
                                حفظ وإضافة آخر
                            </button>
                            
                            <a href="{{ route('admin.question-bank.index') }}" 
                               class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-lg font-medium transition-colors block text-center">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleQuestionFields() {
    const type = document.getElementById('type').value;
    const questionTextWrapper = document.getElementById('question-text-wrapper');
    const imageQuestionNotice = document.getElementById('image-question-notice');
    const questionInput = document.getElementById('question');
    const imageRequiredStar = document.getElementById('image-required-star');
    const imageUrlRequiredStar = document.getElementById('image-url-required-star');
    
    // إخفاء جميع الخيارات
    document.getElementById('question-options').style.display = 'none';
    document.getElementById('multiple-choice-options').style.display = 'none';
    document.getElementById('true-false-options').style.display = 'none';
    document.getElementById('fill-blank-options').style.display = 'none';
    document.getElementById('text-answer-options').style.display = 'none';
    document.getElementById('matching-options').style.display = 'none';
    document.getElementById('ordering-options').style.display = 'none';
    
    // سؤال بصورة: إخفاء نص السؤال وإظهار التنبيه وعلامة مطلوب للصورة
    if (type === 'image_multiple_choice') {
        questionTextWrapper.classList.add('hidden');
        imageQuestionNotice.classList.remove('hidden');
        questionInput.removeAttribute('required');
        imageRequiredStar.classList.remove('hidden');
        imageUrlRequiredStar.classList.remove('hidden');
    } else {
        questionTextWrapper.classList.remove('hidden');
        imageQuestionNotice.classList.add('hidden');
        questionInput.setAttribute('required', 'required');
        imageRequiredStar.classList.add('hidden');
        imageUrlRequiredStar.classList.add('hidden');
    }
    
    // إزالة/إضافة required من خيارات الاختيار المتعدد حتى لا يمنع المتصفح الإرسال عند أنواع أخرى
    const needsMultipleChoice = (type === 'multiple_choice' || type === 'image_multiple_choice');
    for (let i = 1; i <= 5; i++) {
        const opt = document.getElementById('option_' + i);
        if (opt) {
            if (needsMultipleChoice && i <= 2) opt.setAttribute('required', 'required');
            else opt.removeAttribute('required');
        }
    }
    
    if (type) {
        document.getElementById('question-options').style.display = 'block';
        
        switch(type) {
            case 'multiple_choice':
            case 'image_multiple_choice':
                document.getElementById('multiple-choice-options').style.display = 'block';
                break;
            case 'true_false':
                document.getElementById('true-false-options').style.display = 'block';
                break;
            case 'fill_blank':
                document.getElementById('fill-blank-options').style.display = 'block';
                break;
            case 'short_answer':
            case 'essay':
                document.getElementById('text-answer-options').style.display = 'block';
                break;
            case 'matching':
                document.getElementById('matching-options').style.display = 'block';
                break;
            case 'ordering':
                document.getElementById('ordering-options').style.display = 'block';
                break;
        }
    }
    
    updatePreview();
}

function updatePreview() {
    const question = document.getElementById('question').value;
    const type = document.getElementById('type').value;
    const preview = document.getElementById('question-preview');
    
    if (type === 'image_multiple_choice') {
        preview.innerHTML = '<div class="text-right text-gray-700 dark:text-gray-300"><strong>السؤال:</strong> صورة</div>' +
            (document.getElementById('image').files.length ? '<div class="mt-2 text-sm text-green-600">تم اختيار صورة</div>' : '<div class="mt-2 text-sm text-amber-600">ارفع الصورة أو أدخل الرابط</div>');
        if (type === 'multiple_choice' || type === 'image_multiple_choice') {
            let opts = '';
            for (let i = 1; i <= 5; i++) {
                const option = document.getElementById('option_' + i);
                if (option && option.value) opts += '<div class="mt-1">○ ' + option.value + '</div>';
            }
            if (opts) preview.innerHTML += '<div class="mt-3"><strong>الخيارات:</strong>' + opts + '</div>';
        }
        return;
    }
    
    if (!question) {
        preview.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400">اكتب السؤال لرؤية المعاينة</div>';
        return;
    }
    
    let previewHtml = `<div class="text-right"><strong>السؤال:</strong> ${question}</div>`;
    
    if (type === 'multiple_choice') {
        previewHtml += '<div class="mt-3"><strong>الخيارات:</strong>';
        for (let i = 1; i <= 5; i++) {
            const option = document.getElementById(`option_${i}`).value;
            if (option) {
                previewHtml += `<div class="mt-1">○ ${option}</div>`;
            }
        }
        previewHtml += '</div>';
    } else if (type === 'true_false') {
        previewHtml += '<div class="mt-3"><strong>الخيارات:</strong><div class="mt-1">○ صح</div><div class="mt-1">○ خطأ</div></div>';
    }
    
    preview.innerHTML = previewHtml;
}

// تحديث المعاينة عند تغيير المدخلات
document.addEventListener('DOMContentLoaded', function() {
    toggleQuestionFields();
    
    document.getElementById('question').addEventListener('input', updatePreview);
    document.getElementById('type').addEventListener('change', updatePreview);
    
    // تحديث المعاينة عند تغيير الخيارات
    for (let i = 1; i <= 5; i++) {
        const option = document.getElementById(`option_${i}`);
        if (option) {
            option.addEventListener('input', updatePreview);
        }
    }
});
</script>
@endpush
@endsection

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\QuestionBank;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionBankController extends Controller
{
    /**
     * عرض بنك الأسئلة
     */
    public function index(Request $request)
    {
        $query = Question::with(['category', 'questionBank']);

        // فلترة حسب التصنيف
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // فلترة حسب نوع السؤال
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب مستوى الصعوبة
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        // البحث في النص
        if ($request->filled('search')) {
            $query->where('question', 'like', '%' . $request->search . '%');
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate(20);

        // بيانات للفلاتر
        $categories = QuestionCategory::active()->orderBy('name')->get();
        $questionTypes = Question::getQuestionTypes();
        $difficultyLevels = Question::getDifficultyLevels();

        // إحصائيات
        $stats = [
            'total_questions' => Question::count(),
            'active_questions' => Question::active()->count(),
            'categories_count' => QuestionCategory::count(),
            'by_type' => Question::selectRaw('type, count(*) as count')
                               ->groupBy('type')
                               ->pluck('count', 'type')
                               ->toArray(),
        ];

        return view('admin.question-bank.index', compact(
            'questions', 'categories', 'questionTypes', 'difficultyLevels', 'stats'
        ));
    }

    /**
     * عرض صفحة إضافة سؤال جديد
     */
    public function create(Request $request)
    {
        $categories = QuestionCategory::active()->orderBy('name')->get();
        $questionTypes = Question::getQuestionTypes();
        $difficultyLevels = Question::getDifficultyLevels();
        
        // إذا تم تمرير تصنيف محدد
        $selectedCategory = $request->get('category_id');

        return view('admin.question-bank.create', compact(
            'categories', 'questionTypes', 'difficultyLevels', 'selectedCategory'
        ));
    }

    /**
     * حفظ سؤال جديد
     */
    public function store(Request $request)
    {
        if ($request->input('type') === 'true_false' && !$request->filled('true_false_answer')) {
            $request->merge(['true_false_answer' => 'صح']);
        }

        $rules = [
            'category_id' => 'required|exists:question_categories,id',
            'type' => 'required|in:' . implode(',', array_keys(Question::getQuestionTypes())),
            'difficulty_level' => 'required|in:' . implode(',', array_keys(Question::getDifficultyLevels())),
            'points' => 'required|numeric|min:0.5|max:100',
            'time_limit' => 'nullable|integer|min:10|max:600',
            'explanation' => 'nullable|string',
            'tags' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_url' => 'nullable|url',
            'audio_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'is_active' => 'boolean',
        ];

        if ($request->input('type') === 'image_multiple_choice') {
            $rules['question'] = 'nullable|string';
            $rules['image'] = 'required_without:image_url|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
            $rules['image_url'] = 'required_without:image|nullable|url';
        } else {
            $rules['question'] = 'required|string';
        }

        if ($request->input('type') === 'true_false') {
            $rules['true_false_answer'] = 'nullable|in:صح,خطأ';
        }

        if (in_array($request->input('type'), ['multiple_choice', 'image_multiple_choice'], true)) {
            $rules['option_1'] = 'required|string|max:1000';
            $rules['option_2'] = 'required|string|max:1000';
        }

        $request->validate($rules, [
            'category_id.required' => 'التصنيف مطلوب',
            'question.required' => 'نص السؤال مطلوب',
            'type.required' => 'نوع السؤال مطلوب',
            'difficulty_level.required' => 'مستوى الصعوبة مطلوب',
            'points.required' => 'درجة السؤال مطلوبة',
            'points.min' => 'درجة السؤال يجب أن تكون 0.5 على الأقل',
            'points.max' => 'درجة السؤال لا يجب أن تتجاوز 100',
            'image.required_without' => 'يجب رفع صورة السؤال أو إدخال رابط الصورة عند اختيار "سؤال بصورة"',
            'image_url.required_without' => 'يجب رفع صورة السؤال أو إدخال رابط الصورة عند اختيار "سؤال بصورة"',
            'true_false_answer.required' => 'يجب اختيار الإجابة الصحيحة (صح أو خطأ)',
            'true_false_answer.in' => 'الإجابة الصحيحة يجب أن تكون صح أو خطأ',
            'option_1.required' => 'الخيار الأول مطلوب في اختيار متعدد',
            'option_2.required' => 'الخيار الثاني مطلوب في اختيار متعدد',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($data['type'] === 'image_multiple_choice' && empty(trim($data['question'] ?? ''))) {
            $data['question'] = 'سؤال بصورة';
        }
        
        // معالجة التاجز
        if ($request->filled('tags')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }

        // رفع الصورة
        if ($request->hasFile('image')) {
            $data['image_url'] = $this->handleImageUpload($request->file('image'));
        }

        // معالجة الخيارات والإجابات حسب نوع السؤال
        $data = $this->processQuestionData($data, $request);

        $data = array_intersect_key($data, array_fill_keys((new Question)->getFillable(), true));

        Question::create($data);

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'تم إضافة السؤال بنجاح');
    }

    /**
     * عرض تفاصيل السؤال
     */
    public function show(Question $question)
    {
        $question->load(['category', 'questionBank']);
        
        return view('admin.question-bank.show', compact('question'));
    }

    /**
     * عرض صفحة تعديل السؤال
     */
    public function edit(Question $question)
    {
        $categories = QuestionCategory::active()->orderBy('name')->get();
        $questionTypes = Question::getQuestionTypes();
        $difficultyLevels = Question::getDifficultyLevels();

        return view('admin.question-bank.edit', compact(
            'question', 'categories', 'questionTypes', 'difficultyLevels'
        ));
    }

    /**
     * تحديث السؤال
     */
    public function update(Request $request, Question $question)
    {
        if ($request->input('type') === 'true_false' && !$request->filled('true_false_answer')) {
            $request->merge(['true_false_answer' => 'صح']);
        }

        $rules = [
            'category_id' => 'required|exists:question_categories,id',
            'type' => 'required|in:' . implode(',', array_keys(Question::getQuestionTypes())),
            'difficulty_level' => 'required|in:' . implode(',', array_keys(Question::getDifficultyLevels())),
            'points' => 'required|numeric|min:0.5|max:100',
            'time_limit' => 'nullable|integer|min:10|max:600',
            'explanation' => 'nullable|string',
            'tags' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_url' => 'nullable|url',
            'audio_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'is_active' => 'boolean',
        ];

        if ($request->input('type') === 'image_multiple_choice') {
            $rules['question'] = 'nullable|string';
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
            $rules['image_url'] = 'nullable|url';
        } else {
            $rules['question'] = 'required|string';
        }

        if ($request->input('type') === 'true_false') {
            $rules['true_false_answer'] = 'nullable|in:صح,خطأ';
        }

        if (in_array($request->input('type'), ['multiple_choice', 'image_multiple_choice'], true)) {
            $rules['option_1'] = 'required|string|max:1000';
            $rules['option_2'] = 'required|string|max:1000';
        }

        $request->validate($rules, [
            'true_false_answer.required' => 'يجب اختيار الإجابة الصحيحة (صح أو خطأ)',
            'true_false_answer.in' => 'الإجابة الصحيحة يجب أن تكون صح أو خطأ',
            'option_1.required' => 'الخيار الأول مطلوب في اختيار متعدد',
            'option_2.required' => 'الخيار الثاني مطلوب في اختيار متعدد',
        ]);

        if ($request->input('type') === 'image_multiple_choice') {
            $hasImage = $request->hasFile('image') || $request->filled('image_url') || !empty($question->image_url);
            if (!$hasImage) {
                return back()->withErrors(['image' => 'يجب رفع صورة السؤال أو إدخال رابط الصورة عند اختيار "سؤال بصورة"'])->withInput();
            }
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($data['type'] === 'image_multiple_choice' && empty(trim($data['question'] ?? ''))) {
            $data['question'] = 'سؤال بصورة';
        }
        
        // معالجة التاجز
        if ($request->filled('tags')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }

        // معالجة الصور
        if ($request->has('remove_image') && $request->remove_image == '1') {
            // حذف الصورة الحالية
            if ($question->image_url && Storage::disk('public')->exists($question->image_url)) {
                Storage::disk('public')->delete($question->image_url);
            }
            $data['image_url'] = null;
        } elseif ($request->hasFile('image')) {
            // رفع صورة جديدة
            if ($question->image_url && Storage::disk('public')->exists($question->image_url)) {
                Storage::disk('public')->delete($question->image_url);
            }
            
            $data['image_url'] = $this->handleImageUpload($request->file('image'));
        }

        // معالجة الخيارات والإجابات
        $data = $this->processQuestionData($data, $request);

        $data = array_intersect_key($data, array_fill_keys($question->getFillable(), true));

        $question->update($data);

        return redirect()->route('admin.question-bank.show', $question)
            ->with('success', 'تم تحديث السؤال بنجاح');
    }

    /**
     * حذف السؤال
     */
    public function destroy(Question $question)
    {
        // التحقق من عدم استخدام السؤال في امتحانات
        if ($question->examQuestions()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف السؤال لأنه مستخدم في امتحانات');
        }

        // حذف الصورة
        if ($question->image_url && Storage::disk('public')->exists($question->image_url)) {
            Storage::disk('public')->delete($question->image_url);
        }

        $question->delete();

        return redirect()->route('admin.question-bank.index')
            ->with('success', 'تم حذف السؤال بنجاح');
    }

    /**
     * معالجة بيانات السؤال حسب نوعه
     */
    private function processQuestionData($data, $request)
    {
        switch ($data['type']) {
            case 'multiple_choice':
            case 'image_multiple_choice':
                $data['options'] = array_values(array_filter([
                    $request->input('option_1'),
                    $request->input('option_2'),
                    $request->input('option_3'),
                    $request->input('option_4'),
                    $request->input('option_5'),
                ]));
                // تخزين الإجابة الصحيحة كنص الخيار (لأن الطالب يرسل نص الخيار في الامتحان)
                $correctOption = (int) $request->input('correct_option');
                if ($correctOption >= 1 && $correctOption <= 5) {
                    $correctOption--; // نموذج الإضافة يستخدم 1-5
                }
                $correctOption = max(0, min(count($data['options']) - 1, $correctOption));
                $correctText = $data['options'][$correctOption] ?? null;
                $data['correct_answer'] = $correctText ? [$correctText] : [];
                break;

            case 'true_false':
                $data['options'] = ['صح', 'خطأ'];
                $tfAnswer = $request->input('true_false_answer');
                $tfAnswer = in_array($tfAnswer, ['صح', 'خطأ'], true) ? $tfAnswer : 'صح';
                $data['correct_answer'] = [$tfAnswer];
                break;

            case 'fill_blank':
                $data['options'] = null;
                $answers = array_values(array_filter(array_map('trim', explode(',', $request->input('correct_answers', '')))));
                $data['correct_answer'] = $answers;
                break;

            case 'short_answer':
            case 'essay':
                $data['options'] = null;
                $modelAnswer = trim((string) $request->input('model_answer', ''));
                $data['correct_answer'] = $modelAnswer !== '' ? [$modelAnswer] : [];
                break;

            case 'matching':
                $leftItems = array_values(array_filter(array_map('trim', explode("\n", $request->input('left_items', '')))));
                $rightItems = array_values(array_filter(array_map('trim', explode("\n", $request->input('right_items', '')))));
                $data['options'] = [
                    'left' => $leftItems,
                    'right' => $rightItems,
                ];
                $pairs = $request->input('matching_pairs', []);
                $data['correct_answer'] = is_array($pairs) ? $pairs : [];
                break;

            case 'ordering':
                $items = array_values(array_filter(array_map('trim', explode("\n", $request->input('ordering_items', '')))));
                $data['options'] = $items;
                $order = $request->input('correct_order', []);
                $data['correct_answer'] = is_array($order) ? $order : [];
                break;

            default:
                $data['options'] = $data['options'] ?? null;
                $data['correct_answer'] = is_array($data['correct_answer'] ?? null) ? $data['correct_answer'] : [];
                break;
        }

        // التأكد من أن correct_answer دائماً مصفوفة (العمود يقبل JSON وليس null)
        if (!isset($data['correct_answer']) || !is_array($data['correct_answer'])) {
            $data['correct_answer'] = [];
        }

        return $data;
    }

    /**
     * تصدير الأسئلة
     */
    public function export(Request $request)
    {
        // يمكن تطوير هذه الوظيفة لتصدير الأسئلة إلى Excel أو JSON
        return response()->json(['message' => 'سيتم تطوير وظيفة التصدير قريباً']);
    }

    /**
     * استيراد الأسئلة
     */
    public function import(Request $request)
    {
        // يمكن تطوير هذه الوظيفة لاستيراد الأسئلة من Excel أو JSON
        return response()->json(['message' => 'سيتم تطوير وظيفة الاستيراد قريباً']);
    }

    /**
     * نسخ السؤال
     */
    public function duplicate(Question $question)
    {
        $newQuestion = $question->replicate();
        $newQuestion->question = $question->question . ' - نسخة';
        $newQuestion->save();

        return redirect()->route('admin.question-bank.edit', $newQuestion)
            ->with('success', 'تم نسخ السؤال بنجاح');
    }

    /**
     * معالجة رفع الصور مع تحسين الجودة والحجم (دعم: jpeg, png, gif, webp)
     */
    private function handleImageUpload($imageFile)
    {
        $ext = strtolower($imageFile->getClientOriginalExtension() ?: 'jpg');
        $fileName = uniqid('question_') . '.' . $ext;

        $storagePath = 'questions/' . date('Y/m');
        if (!Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->makeDirectory($storagePath);
        }

        $imageFile->storeAs($storagePath, $fileName, 'public');
        $fullStoragePath = storage_path('app/public/' . $storagePath . '/' . $fileName);

        try {
            $imageData = @getimagesize($fullStoragePath);
            if (!$imageData) {
                return $storagePath . '/' . $fileName;
            }
            $mimeType = $imageData['mime'];

            switch ($mimeType) {
                case 'image/jpeg':
                    $image = @imagecreatefromjpeg($fullStoragePath);
                    if ($image) {
                        $image = $this->optimizeImage($image);
                        imagejpeg($image, $fullStoragePath, 85);
                        imagedestroy($image);
                    }
                    break;
                case 'image/png':
                    $image = @imagecreatefrompng($fullStoragePath);
                    if ($image) {
                        $image = $this->optimizeImage($image, true);
                        imagepng($image, $fullStoragePath, 9);
                        imagedestroy($image);
                    }
                    break;
                case 'image/webp':
                    $image = @imagecreatefromwebp($fullStoragePath);
                    if ($image) {
                        $image = $this->optimizeImage($image, true);
                        imagewebp($image, $fullStoragePath, 85);
                        imagedestroy($image);
                    }
                    break;
                case 'image/gif':
                    break;
            }
        } catch (\Throwable $e) {
            \Log::warning('فشل في تحسين الصورة: ' . $e->getMessage());
        }

        return $storagePath . '/' . $fileName;
    }

    /**
     * تحسين الصورة (تقليل الأبعاد إذا تجاوزت الحد الأقصى)
     */
    private function optimizeImage($image, bool $preserveAlpha = false)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $maxWidth = 1200;
        $maxHeight = 1200;

        if ($width <= $maxWidth && $height <= $maxHeight) {
            return $image;
        }

        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int) round($width * $ratio);
        $newHeight = (int) round($height * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        if (!$resized) {
            return $image;
        }

        if ($preserveAlpha) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        return $resized;
    }
}
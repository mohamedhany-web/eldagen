<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseLesson;
use App\Models\LessonVideoQuestion;
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;

class LessonVideoQuestionController extends Controller
{
    public function index(AdvancedCourse $course, CourseLesson $lesson)
    {
        if ($lesson->advanced_course_id != $course->id) {
            abort(404);
        }
        $items = $lesson->videoQuestions()->with('question')->orderBy('time_seconds')->get();
        return response()->json(['items' => $items]);
    }

    public function store(Request $request, AdvancedCourse $course, CourseLesson $lesson)
    {
        if ($lesson->advanced_course_id != $course->id) {
            abort(404);
        }
        $request->validate([
            'time_seconds' => 'required|integer|min:0',
            'question_id' => 'required|exists:questions,id',
            'on_wrong' => 'required|in:restart_video,rewind_to_previous,training',
        ]);

        $order = $lesson->videoQuestions()->max('order') + 1;
        $lesson->videoQuestions()->create([
            'time_seconds' => $request->time_seconds,
            'question_id' => $request->question_id,
            'on_wrong' => $request->on_wrong,
            'order' => $order,
        ]);

        return response()->json(['success' => true, 'message' => 'تمت إضافة السؤال في الفيديو']);
    }

    public function destroy(AdvancedCourse $course, CourseLesson $lesson, LessonVideoQuestion $videoQuestion)
    {
        if ($lesson->advanced_course_id != $course->id || $videoQuestion->course_lesson_id != $lesson->id) {
            abort(404);
        }
        $videoQuestion->delete();
        return response()->json(['success' => true, 'message' => 'تم الحذف']);
    }

    /**
     * للوحة اختيار السؤال من البنك: التصنيفات
     */
    public function bankCategories()
    {
        $categories = QuestionCategory::withCount('questions')
            ->main()
            ->orderBy('order')
            ->with(['children' => fn ($q) => $q->withCount('questions')->orderBy('order')])
            ->get();
        return response()->json($categories);
    }

    /**
     * للوحة اختيار السؤال من البنك: الأسئلة حسب التصنيف
     */
    public function bankQuestions(Request $request)
    {
        $query = Question::with('category')->active();
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $query->where('question', 'like', '%' . $request->search . '%');
        }
        $questions = $query->orderBy('created_at', 'desc')->limit(100)->get();
        return response()->json($questions);
    }

    /**
     * معاينة السؤال (HTML للبوب اب)
     */
    public function questionPreview(Question $question)
    {
        $types = Question::getQuestionTypes();
        return view('admin.lesson-video-questions.partials.question-preview', [
            'question' => $question,
            'typeLabel' => $types[$question->type] ?? $question->type,
        ]);
    }
}

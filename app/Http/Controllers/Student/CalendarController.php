<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * عرض التقويم بأحداث حقيقية (امتحانات الكورسات المسجل فيها الطالب)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $enrolledCourseIds = $user->courseEnrollments()
            ->where('status', 'active')
            ->pluck('advanced_course_id');

        $events = collect();
        if ($enrolledCourseIds->isNotEmpty()) {
            $exams = Exam::whereIn('advanced_course_id', $enrolledCourseIds)
                ->where('is_active', true)
                ->where('is_published', true)
                ->whereNotNull('start_time')
                ->with('course')
                ->orderBy('start_time')
                ->get();

            foreach ($exams as $exam) {
                $events->push((object)[
                    'id' => 'exam-' . $exam->id,
                    'title' => $exam->title,
                    'date' => Carbon::parse($exam->start_time),
                    'type' => 'exam',
                    'color' => 'red',
                    'url' => route('student.exams.show', $exam),
                    'course_title' => $exam->course->title ?? '',
                ]);
            }
        }

        // الشهر المعروض (من الطلب أو الشهر الحالي)
        $year = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);
        $currentMonth = Carbon::createFromDate($year, $month, 1);
        $prevMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        // بناء شبكة أيام الشهر (أحد = 0 في Carbon)
        $firstDay = $currentMonth->copy()->startOfMonth();
        $lastDay = $currentMonth->copy()->endOfMonth();
        $startEmpty = (int) $firstDay->format('w'); // 0 = الأحد
        $totalDays = $lastDay->day;
        $weeks = [];
        $day = 1;
        $totalCells = max(35, (int) (ceil(($startEmpty + $totalDays) / 7) * 7));
        $totalCells = max(35, (int) $totalCells);
        for ($i = 0; $i < $totalCells; $i++) {
            if ($i < $startEmpty || $day > $totalDays) {
                $weeks[] = ['day' => null, 'date' => null, 'is_today' => false, 'event_count' => 0];
            } else {
                $date = Carbon::createFromDate($year, $month, $day);
                $eventCount = $events->filter(function ($e) use ($date) {
                    return $e->date->format('Y-m-d') === $date->format('Y-m-d');
                })->count();
                $weeks[] = [
                    'day' => $day,
                    'date' => $date->format('Y-m-d'),
                    'is_today' => $date->isToday(),
                    'event_count' => $eventCount,
                ];
                $day++;
            }
        }

        return view('student.calendar.index', compact(
            'events',
            'currentMonth',
            'prevMonth',
            'nextMonth',
            'weeks'
        ));
    }
}
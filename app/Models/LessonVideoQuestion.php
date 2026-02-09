<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonVideoQuestion extends Model
{
    public const ON_WRONG_RESTART_VIDEO = 'restart_video';
    public const ON_WRONG_REWIND_PREVIOUS = 'rewind_to_previous';
    public const ON_WRONG_TRAINING = 'training';

    protected $fillable = [
        'course_lesson_id',
        'time_seconds',
        'question_id',
        'on_wrong',
        'order',
    ];

    protected $casts = [
        'time_seconds' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(LessonVideoQuestionAnswer::class, 'lesson_video_question_id');
    }

    public static function onWrongOptions(): array
    {
        return [
            self::ON_WRONG_TRAINING => 'سؤال تدريبي (بدون عقوبة)',
            self::ON_WRONG_REWIND_PREVIOUS => 'إعادة من السؤال السابق',
            self::ON_WRONG_RESTART_VIDEO => 'إعادة الفيديو من البداية',
        ];
    }

    public function getOnWrongLabelAttribute(): string
    {
        return self::onWrongOptions()[$this->on_wrong] ?? $this->on_wrong;
    }

    public function getTimeFormattedAttribute(): string
    {
        $m = (int) floor($this->time_seconds / 60);
        $s = $this->time_seconds % 60;
        return $m . ':' . str_pad((string) $s, 2, '0', STR_PAD_LEFT);
    }
}

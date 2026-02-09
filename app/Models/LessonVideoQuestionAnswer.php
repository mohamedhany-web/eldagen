<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonVideoQuestionAnswer extends Model
{
    protected $fillable = [
        'user_id',
        'lesson_video_question_id',
        'answer',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lessonVideoQuestion(): BelongsTo
    {
        return $this->belongsTo(LessonVideoQuestion::class, 'lesson_video_question_id');
    }
}

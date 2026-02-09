<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'icon',
        'color',
        'order',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subjects()
    {
        return $this->hasMany(AcademicSubject::class);
    }

    public function academicSubjects()
    {
        return $this->hasMany(AcademicSubject::class);
    }

    public function courses()
    {
        return $this->hasMany(AdvancedCourse::class);
    }

    public function advancedCourses()
    {
        return $this->hasMany(AdvancedCourse::class);
    }

    public function questionCategories()
    {
        return $this->hasMany(QuestionCategory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function getActiveSubjectsCountAttribute()
    {
        return $this->subjects()->active()->count();
    }

    public function getActiveCoursesCountAttribute()
    {
        return $this->courses()->active()->count();
    }
}
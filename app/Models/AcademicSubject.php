<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'name',
        'code',
        'description',
        'icon',
        'color',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
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

    public function getActiveCoursesCountAttribute()
    {
        return $this->courses()->active()->count();
    }

    public function getFullNameAttribute()
    {
        return $this->academicYear->name . ' - ' . $this->name;
    }
}
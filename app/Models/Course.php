<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Internship;

class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'vacancies',
    ];

    public function interns(): HasMany
    {
        return $this->hasMany(Internship::class);
    }

    public function getVacanciesUsedAttribute(): int
    {
        return $this->interns()->count();
    }

    public function getVacanciesAvailableAttribute(): int
    {
        return max(0, $this->vacancies - $this->vacancies_used);
    }

    public function getUsagePercentageAttribute(): float
    {
        if ($this->vacancies === 0) return 0;
        return ($this->vacancies_used / $this->vacancies) * 100;
    }

    public function isNearLimit(): bool
    {
        return $this->usage_percentage >= 50;
    }

    public function scopeNearLimit($query)
    {
        return $query->whereRaw('CAST((
            SELECT COUNT(*)
            FROM internships
            WHERE internships.course_id = courses.id
        ) AS DECIMAL(10,2)) / CAST(courses.vacancies AS DECIMAL(10,2)) * 100 >= ?', [50]);
    }
}

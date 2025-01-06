<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Intern;

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
        return $this->hasMany(Intern::class);
    }

    public function getVacanciesUsedAttribute(): int
    {
        return $this->interns()->count();
    }

    public function getVacanciesAvailableAttribute(): int
    {
        return max(0, $this->vacancies - $this->interns()->count());
    }
}

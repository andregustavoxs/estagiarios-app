<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Intern extends Model
{
    /** @use HasFactory<\Database\Factories\InternFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'photo',
        'supervisor_id',
        'department_id',
        'course_id',
        'internship_agency_id',
    ];

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function internshipAgency(): BelongsTo
    {
        return $this->belongsTo(InternshipAgency::class);
    }

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class);
    }

    public function scopeOrderByVacationStatus(Builder $query, string $direction): Builder
    {
        $today = now()->format('Y-m-d');

        return $query->orderBy(function ($query) use ($today) {
            return $query->selectRaw('EXISTS (
                SELECT 1 
                FROM internships i 
                JOIN intern_vacations v ON v.internship_id = i.id 
                WHERE i.intern_id = interns.id 
                AND DATE(v.start_date) <= ? 
                AND DATE(v.end_date) >= ?
            )', [$today, $today]);
        }, $direction);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    public function isCurrentlyOnVacation(): bool
    {
        return $this->internships()
            ->whereHas('vacations', function ($query) {
                $query->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now());
            })
            ->exists();
    }
}

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
        'registration_number',
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

    public function vacations(): HasMany
    {
        return $this->hasMany(InternVacation::class);
    }

    public function scopeOrderByVacationStatus(Builder $query, string $direction): Builder
    {
        $today = now()->startOfDay();
        
        return $query
            ->leftJoin('intern_vacations', function ($join) use ($today) {
                $join->on('interns.id', '=', 'intern_vacations.intern_id')
                    ->where('intern_vacations.start_date', '<=', $today)
                    ->where('intern_vacations.end_date', '>=', $today);
            })
            ->orderByRaw('CASE WHEN intern_vacations.id IS NOT NULL THEN 1 ELSE 0 END ' . $direction)
            ->select('interns.*');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    public function isCurrentlyOnVacation(): bool
    {
        $today = now()->startOfDay();
        return $this->vacations()
            ->where(function ($query) use ($today) {
                $query->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today);
            })
            ->exists();
    }
}

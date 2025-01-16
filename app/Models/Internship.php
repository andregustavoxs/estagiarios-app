<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Internship extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'registration_number',
        'department_id',
        'supervisor_id',
        'course_id',
        'internship_agency_id',
    ];

    public function intern(): BelongsTo
    {
        return $this->belongsTo(Intern::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
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

    public function hasActiveVacation(): bool
    {
        return $this->vacations()
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->exists();
    }
}

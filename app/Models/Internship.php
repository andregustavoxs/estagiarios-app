<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Internship extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $with = ['intern'];

    protected $fillable = [
        'registration_number',
        'intern_id',
        'course_id',
        'department_id',
        'supervisor_id',
        'educational_institution_id',
        'start_date',
        'end_date',
        'status',
        'education_level',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'string',
    ];

    public function intern(): BelongsTo
    {
        return $this->belongsTo(Intern::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function educationalInstitution(): BelongsTo
    {
        return $this->belongsTo(EducationalInstitution::class);
    }

    public function vacations(): HasMany
    {
        return $this->hasMany(InternVacation::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(InternEvaluation::class);
    }

    public function commitmentTerm(): HasOne
    {
        return $this->hasOne(CommitmentTerm::class);
    }

    public function addendums(): HasMany
    {
        return $this->hasMany(InternAddendum::class);
    }

    public function hasActiveVacation(): bool
    {
        return $this->vacations()
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->exists();
    }

    public function hasFirstEvaluation(): bool
    {
        return $this->evaluations()
            ->where('evaluation_number', 1)
            ->where('is_completed', true)
            ->exists();
    }
}

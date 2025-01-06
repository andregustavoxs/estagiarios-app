<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function dossier(): HasOne
    {
        return $this->hasOne(InternDossier::class);
    }
}

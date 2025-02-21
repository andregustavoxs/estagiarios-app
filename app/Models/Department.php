<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Internship;
use App\Models\Supervisor;
use App\Models\Extension;

class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'acronym',
        'supervisor_id',
    ];
    public function interns(): HasMany
    {
        return $this->hasMany(Internship::class);
    }

    public function supervisors(): HasMany
    {
        return $this->hasMany(Supervisor::class);
    }

    public function extensions(): HasMany
    {
        return $this->hasMany(Extension::class);
    }
}

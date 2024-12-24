<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Intern extends Model
{
    /** @use HasFactory<\Database\Factories\InternFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'supervisor_id',
        'department_id',
    ];

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}

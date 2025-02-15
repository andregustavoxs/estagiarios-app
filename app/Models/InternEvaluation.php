<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternEvaluation extends Model
{
    protected $fillable = [
        'internship_id',
        'evaluation_number',
        'is_completed',
        'pdf_path',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function getEvaluationTypeAttribute(): string
    {
        return match($this->evaluation_number) {
            1 => '1ª Avaliação',
            2 => '2ª Avaliação',
            3 => '3ª Avaliação',
            4 => '4ª Avaliação',
            default => 'Avaliação',
        };
    }
}

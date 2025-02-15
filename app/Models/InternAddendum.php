<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternAddendum extends Model
{
    protected $fillable = [
        'internship_id',
        'addendum_number',
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

    public function getAddendumTypeAttribute(): string
    {
        return match($this->addendum_number) {
            1 => '1º Aditivo',
            2 => '2º Aditivo',
            3 => '3º Aditivo',
            default => 'Aditivo',
        };
    }
}

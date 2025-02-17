<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agreement extends Model
{
    /** @use HasFactory<\Database\Factories\AgreementFactory> */
    use HasFactory;

    protected $fillable = [
        'internship_agency_id',
        'agreement_number',
        'agreement_validity_start',
        'agreement_validity_end',
    ];

    protected $casts = [
        'agreement_validity_start' => 'date',
        'agreement_validity_end' => 'date',
    ];

    public function internshipAgency(): BelongsTo
    {
        return $this->belongsTo(InternshipAgency::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitmentTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id',
        'intern_signature',
        'intern_signature_date',
        'court_signature',
        'court_signature_date',
        'institution_signature',
        'institution_signature_date',
        'document_path',
        'observations',
    ];

    protected $casts = [
        'intern_signature' => 'boolean',
        'court_signature' => 'boolean',
        'institution_signature' => 'boolean',
        'intern_signature_date' => 'datetime',
        'court_signature_date' => 'datetime',
        'institution_signature_date' => 'datetime',
    ];

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function isFullySigned(): bool
    {
        return $this->intern_signature && 
               $this->court_signature && 
               $this->institution_signature;
    }

    public function getPendingSignatures(): array
    {
        $pending = [];

        if (!$this->intern_signature) {
            $pending[] = 'Estagiário';
        }
        if (!$this->court_signature) {
            $pending[] = 'Tribunal de Contas';
        }
        if (!$this->institution_signature) {
            $pending[] = 'Instituição de Ensino';
        }

        return $pending;
    }
}

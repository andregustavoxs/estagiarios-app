<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class InternshipAgency extends Model
{
    /** @use HasFactory<\Database\Factories\InternshipAgencyFactory> */
    use HasFactory;

    protected $fillable = [
        'cnpj',
        'company_name',
        'trade_name',
        'phone',
        'contact_person',
    ];

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class);
    }

    public function interns(): HasManyThrough
    {
        return $this->hasManyThrough(
            Intern::class,
            Internship::class,
            'internship_agency_id', // Foreign key on internships table
            'id', // Foreign key on interns table
            'id', // Local key on internship_agencies table
            'intern_id' // Local key on internships table
        );
    }
}

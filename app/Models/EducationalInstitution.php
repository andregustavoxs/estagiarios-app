<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class EducationalInstitution extends Model
{
    /** @use HasFactory<\Database\Factories\EducationalInstitutionFactory> */
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
            'educational_institution_id', // Foreign key on internships table
            'id', // Foreign key on interns table
            'id', // Local key on educational_institutions table
            'intern_id' // Local key on internships table
        );
    }
}

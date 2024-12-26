<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function interns(): HasMany
    {
        return $this->hasMany(Intern::class);
    }
}

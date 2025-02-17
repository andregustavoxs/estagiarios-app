<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Extension extends Model
{
    /** @use HasFactory<\Database\Factories\ExtensionFactory> */
    use HasFactory;

    protected $fillable = [
        'department_id',
        'extension',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}

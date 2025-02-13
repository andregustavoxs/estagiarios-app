<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EvaluationTemplate extends Model
{
    protected $fillable = [
        'name',
        'file_path',
        'description',
    ];

    protected $casts = [
    ];

    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    public function getStorageFilePath()
    {
        return storage_path('app/public/' . $this->file_path);
    }
}

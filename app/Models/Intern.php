<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intern extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'photo',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class);
    }

    public function isCurrentlyOnVacation(): bool
    {
        return $this->internships()
            ->whereHas('vacations', function (Builder $query) {
                $query->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now());
            })
            ->exists();
    }

    public function scopeOrderByVacationStatus(Builder $query, string $direction): Builder
    {
        $today = now()->format('Y-m-d');

        return $query->orderBy(function ($query) use ($today) {
            return $query->selectRaw('EXISTS (
                SELECT 1 
                FROM internships i 
                JOIN intern_vacations v ON v.internship_id = i.id 
                WHERE i.intern_id = interns.id 
                AND DATE(v.start_date) <= ? 
                AND DATE(v.end_date) >= ?
            )', [$today, $today]);
        }, $direction);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%");
    }
}

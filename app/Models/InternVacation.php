<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternVacation extends Model
{
    protected $fillable = [
        'intern_id',
        'start_date',
        'end_date',
        'observation',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    public function intern(): BelongsTo
    {
        return $this->belongsTo(Intern::class);
    }

    public function isCurrentlyOnVacation(): bool
    {
        $today = Carbon::now()->startOfDay();
        return $today->between(
            Carbon::parse($this->start_date)->startOfDay(),
            Carbon::parse($this->end_date)->endOfDay()
        );
    }

    protected function setStartDateAttribute($value)
    {
        if (is_string($value) && str_contains($value, '/')) {
            $parts = explode('/', $value);
            if (count($parts) === 3) {
                $this->attributes['start_date'] = "{$parts[2]}-{$parts[1]}-{$parts[0]}";
                return;
            }
        }
        $this->attributes['start_date'] = $value;
    }

    protected function setEndDateAttribute($value)
    {
        if (is_string($value) && str_contains($value, '/')) {
            $parts = explode('/', $value);
            if (count($parts) === 3) {
                $this->attributes['end_date'] = "{$parts[2]}-{$parts[1]}-{$parts[0]}";
                return;
            }
        }
        $this->attributes['end_date'] = $value;
    }
}

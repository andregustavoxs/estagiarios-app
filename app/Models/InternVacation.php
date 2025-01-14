<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class InternVacation extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'period',
        'start_date',
        'end_date',
        'days_taken',
        'observation',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_taken' => 'integer',
        'period' => 'integer',
    ];

    protected $appends = ['remaining_days_until_end', 'vacation_status', 'remaining_days_for_period'];

    public function intern(): BelongsTo
    {
        return $this->belongsTo(Intern::class);
    }

    protected static function booted(): void
    {
        static::saving(function ($vacation) {
            if ($vacation->start_date && $vacation->end_date) {
                $vacation->days_taken = $vacation->start_date->diffInDays($vacation->end_date) + 1;
                
                // Check if the date range is valid
                if ($vacation->end_date < $vacation->start_date) {
                    throw ValidationException::withMessages([
                        'end_date' => ['A data de término não pode ser anterior à data de início.'],
                    ]);
                }

                // Calculate total days taken including this vacation
                $totalDays = $vacation->intern->vacations()
                    ->where('id', '!=', $vacation->id)
                    ->where('period', $vacation->period)
                    ->sum('days_taken') + $vacation->days_taken;

                if ($totalDays > 30) {
                    throw ValidationException::withMessages([
                        'end_date' => ['O total de dias de férias não pode exceder 30 dias para este período. Dias restantes: ' . 
                            (30 - ($totalDays - $vacation->days_taken)) . ' dias.'],
                    ]);
                }
            }
        });
    }

    public function setStartDateAttribute($value)
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

    public function setEndDateAttribute($value)
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

    public function isOverlapping(): bool
    {
        $query = static::where('intern_id', $this->intern_id)
            // Remove period check to validate overlaps across all periods
            ->where(function ($query) {
                $query->whereBetween('start_date', [$this->start_date, $this->end_date])
                    ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                    ->orWhere(function ($query) {
                        $query->where('start_date', '<=', $this->start_date)
                            ->where('end_date', '>=', $this->end_date);
                    });
            });

        // If this is an existing vacation (has an ID), exclude it from the check
        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        return $query->exists();
    }

    public function getRemainingDaysUntilEndAttribute(): ?int
    {
        if (!$this->isCurrentlyOnVacation()) {
            return null;
        }

        return now()->startOfDay()->diffInDays($this->end_date) + 1;
    }

    public function getVacationStatusAttribute(): string
    {
        $today = now()->startOfDay();
        
        if ($today->isBefore($this->start_date)) {
            return 'Férias futuras';
        }
        
        if ($today->isAfter($this->end_date)) {
            return 'Férias concluídas';
        }
        
        $remainingDays = $this->remaining_days_until_end;
        return "Em férias (Restam {$remainingDays} " . ($remainingDays > 1 ? 'dias' : 'dia') . ")";
    }

    public function isCurrentlyOnVacation(): bool
    {
        $today = now()->startOfDay();
        return $today->between(
            $this->start_date->startOfDay(),
            $this->end_date->endOfDay()
        );
    }

    public function getRemainingDaysForPeriodAttribute(): int
    {
        $totalDaysTaken = $this->intern->vacations()
            ->where('period', $this->period)
            ->where('id', '!=', $this->id)
            ->sum('days_taken');

        return max(0, 30 - $totalDaysTaken);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Intern;
use App\Models\InternVacation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckInternVacations extends Command
{
    protected $signature = 'app:check-intern-vacations';
    protected $description = 'Check which interns are currently on vacation';

    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        $activeVacations = InternVacation::query()
            ->with(['internship.intern'])
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        if ($activeVacations->isEmpty()) {
            $this->info('Nenhum estagiário está de férias hoje.');
            return;
        }

        $this->info('Estagiários atualmente de férias:');
        $this->newLine();

        foreach ($activeVacations as $vacation) {
            $intern = $vacation->internship->intern;
            $remainingDays = $vacation->remaining_days_until_end;

            $this->line("Nome: {$intern->name}");
            $this->line("Período: {$vacation->period}º Período");
            $this->line("Início: " . $vacation->start_date->format('d/m/Y'));
            $this->line("Término: " . $vacation->end_date->format('d/m/Y'));
            $this->line("Dias restantes: {$remainingDays}");
            
            if ($vacation->observation) {
                $this->line("Observação: {$vacation->observation}");
            }
            
            $this->newLine();
        }
    }
}

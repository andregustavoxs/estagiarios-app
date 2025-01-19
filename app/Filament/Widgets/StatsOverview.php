<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\Intern;
use App\Models\Internship;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // Get active interns count
        $activeInternsCount = Intern::where('status', 'active')->count();
        
        // Get courses close to limit
        $coursesNearLimit = Course::whereRaw('(
            SELECT COUNT(*) 
            FROM internships 
            WHERE internships.course_id = courses.id
        ) >= courses.vacancies * 0.8')->count();

        // Get interns on vacation
        $internsOnVacation = Intern::whereHas('internships', function ($query) {
            $query->whereHas('vacations', function ($query) {
                $query->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now());
            });
        })->count();

        return [
            Stat::make('Estagiários Ativos', $activeInternsCount)
                ->description('Total de estagiários atualmente ativos')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, $activeInternsCount]),

            Stat::make('Cursos Próximos do Limite', $coursesNearLimit)
                ->description('Cursos com 80% ou mais das vagas preenchidas')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning')
                ->chart([2, 3, 3, 4, 3, $coursesNearLimit]),

            Stat::make('Estagiários de Férias', $internsOnVacation)
                ->description('Estagiários atualmente de férias')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info')
                ->chart([1, 2, 2, 1, 3, $internsOnVacation]),
        ];
    }
}
<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => [
                    50 => '244, 253, 244',  // #f4fdf4
                    100 => '230, 240, 247',
                    200 => '190, 210, 230',
                    300 => '150, 180, 213',
                    400 => '110, 150, 196',
                    500 => '44, 115, 169',   // #2c73a9
                    600 => '35, 92, 135',
                    700 => '26, 69, 101',
                    800 => '17, 46, 67',
                    900 => '8, 23, 33',
                    950 => '4, 11, 16',
                ],
                'danger' => [
                    50 => '254, 242, 242',
                    100 => '254, 226, 226',
                    200 => '254, 202, 202',
                    300 => '252, 165, 165',
                    400 => '239, 28, 41',     // #ef1c29
                    500 => '239, 28, 41',     // #ef1c29
                    600 => '220, 38, 38',
                    700 => '185, 28, 28',
                    800 => '153, 27, 27',
                    900 => '127, 29, 29',
                    950 => '69, 10, 10',
                ],
            ])
            ->brandName('TCE-MA')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('4rem')
            ->navigationGroups([
                'Estagiários',
                'Configurações',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\ActiveInternsTable::class,
                \App\Filament\Widgets\CoursesNearLimitTable::class,
                \App\Filament\Widgets\InternsOnVacationTable::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

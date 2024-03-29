<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
// use Awcodes\FilamentStickyHeader\StickyHeaderPlugin;
use Illuminate\Cookie\Middleware\EncryptCookies;
// use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            // ->registration()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->maxContentWidth(MaxWidth::Full)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn (): string => __('blogs'))
                    ->icon('heroicon-o-book-open')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(fn (): string => __('settings'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->plugins([
                FilamentBackgroundsPlugin::make()
                    // ->imageProvider(
                    //     MyImages::make()
                    //         ->directory('images/backgrounds')
                    // )
                    ->remember(900)
                    ->showAttribution(false),
                // StickyHeaderPlugin::make()
                    // ->floating()
                    // ->colored(),

            ]);
    }
}

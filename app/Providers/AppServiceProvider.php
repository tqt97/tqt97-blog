<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['vi', 'en']) // also accepts a closure
                // ->visible(outsidePanels: true)
                // ->flags([
                //     'ar' => asset('flags/saudi-arabia.svg'),
                //     'fr' => asset('flags/france.svg'),
                //     'en' => asset('flags/usa.svg'),
                // ])
                ->circular()
                ->displayLocale('vi');
        });
    }
}

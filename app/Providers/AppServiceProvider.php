<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Child;
use App\Models\VaccinationRecord;
use App\Models\VaccineInventory;
use App\Observers\ChildObserver;
use App\Observers\VaccinationRecordObserver;
use App\Observers\VaccineInventoryObserver;
use Illuminate\Support\Facades\URL;

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
        // Register Observers to automatically generate notifications
        Child::observe(ChildObserver::class);
        VaccinationRecord::observe(VaccinationRecordObserver::class);
        VaccineInventory::observe(VaccineInventoryObserver::class);

        // Force HTTPS on Render (production environment)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('*', function ($view) {
            $brand = Setting::getBrandSettings();

            $view->with([
                'appBrandName' => $brand['name'],
                'appBrandSlogan' => $brand['slogan'],
                'appBrandLogoPath' => $brand['logo_path'],
                'appBrandLogoUrl' => $brand['logo_url'],
                'appOfficeName' => $brand['office_name'],
            ]);
        });
    }
}

<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\SiteSetting;
use App\Support\LocaleSwitcher;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        View::composer(['layouts.public', 'public.*'], function ($view): void {
            $view->with([
                'siteSettings' => SiteSetting::current(),
                'localeUrls' => LocaleSwitcher::allUrls(),
                'navItems' => MenuItem::published()->ordered()->get(),
            ]);
        });

        $this->registerRateLimiters();
    }

    protected function registerRateLimiters(): void
    {
        // Antispam básico del formulario de contacto público: máximo 5
        // envíos por hora por IP. El mensaje de la respuesta 429 va en el
        // idioma activo, para que el JS del formulario lo muestre tal cual.
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perHour(5)->by($request->ip())
                ->response(fn () => response()->json([
                    'message' => __('site.contact.rate_limited'),
                ], 429));
        });
    }
}

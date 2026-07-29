<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Currency;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFour();

        if (!$this->app->runningInConsole()) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('currencies')) {
                    $afn = \App\Currency::where('code', 'AFN')->first();
                    // The legacy system expected 'amount' to be "How many AFN = 1 USD"
                    // Our new system stores "How many USD = 1 AFN"
                    $legacyRate = ($afn && $afn->exchange_rate > 0) ? (1 / $afn->exchange_rate) : 70;
                    View::share('currency', $legacyRate);
                }
            } catch (\Throwable $e) {
                // Silently ignore if DB is not reachable during early boot
            }
        }
    }
}

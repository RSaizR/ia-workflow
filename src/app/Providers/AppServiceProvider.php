<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\AI\QuoteInterpreterInterface;
use App\Services\AI\FakeQuoteInterpreter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            QuoteInterpreterInterface::class,
            FakeQuoteInterpreter::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

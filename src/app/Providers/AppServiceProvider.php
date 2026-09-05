<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\AI\QuoteInterpreterInterface;
use App\Services\AI\OpenAIQuoteInterpreter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            QuoteInterpreterInterface::class,
            OpenAIQuoteInterpreter::class,
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

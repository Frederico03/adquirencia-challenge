<?php

namespace App\Providers;

use App\Services\Adquirencia\AdquirenciaResolve;
use Illuminate\Support\ServiceProvider;

class AdquirenciaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AdquirenciaResolve::class);
    }
}

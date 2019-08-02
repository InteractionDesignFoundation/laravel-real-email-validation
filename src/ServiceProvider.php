<?php

namespace IDF\RealEmailValidation;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/realEmailValidation'),
        ]);
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'realEmailValidation');
    }
}

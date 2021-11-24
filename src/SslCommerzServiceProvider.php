<?php

namespace Hridoy\SslCommerz;

use Illuminate\Support\ServiceProvider;

class SslCommerzServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/sslcommerz.php' => config_path('sslcommerz.php')
        ], 'config');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sslcommerz');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/sslcommerz.php', 'sslcommerz'
        );
    }
}

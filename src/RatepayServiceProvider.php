<?php

namespace Stegback\Ratepay;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class RatepayServiceProvider extends ServiceProvider
{
    public function boot()
    {

        if (File::exists(__DIR__ . '\app\CommonHelper.php')) {
            require __DIR__ . '\app\CommonHelper.php';
        }
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        $this->loadViewsFrom(resource_path('views'), 'laravel');
        $this->loadViewsFrom(__DIR__ . '/views', 'stegback-ratepay-views');

        $this->publishes([
            __DIR__.'/views' => resource_path('/views'),
        ], 'stegback-ratepay-views');
    }

    public function register()
    {
        //
    }
}

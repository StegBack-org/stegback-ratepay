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
        $this->loadViewsFrom(__DIR__ . '/views', 'Ratepay');
    }

    public function register()
    {
        //
    }
}

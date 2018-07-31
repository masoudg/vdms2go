<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\VoucherCodeServiceImpl;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Services\VoucherCodeService', function ($app) {
          return new VoucherCodeServiceImpl();
        });
    }
}

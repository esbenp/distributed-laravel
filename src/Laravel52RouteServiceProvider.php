<?php

namespace Optimus\Api\System;

use Illuminate\Routing\Router;

abstract class Laravel52RouteServiceProvider extends Providers\RouteServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadConfig();

        parent::boot($router);
    }
}

<?php

namespace Optimus\Api\System;

abstract class Laravel53RouteServiceProvider extends Providers\RouteServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadConfig();

        parent::boot();
    }
}

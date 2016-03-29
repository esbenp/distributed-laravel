<?php

namespace Optimus\Api\System;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadConfig();

        parent::boot($router);
    }

    public function register()
    {
        $this->registerAssets();
    }

    private function registerAssets()
    {
        $this->publishes([
            __DIR__.'/../config/optimus.components.php' => config_path('optimus.components.php')
        ]);
    }

    private function loadConfig()
    {
        if ($this->app['config']->get('optimus.components') === null) {
            $this->app['config']->set('optimus.components', require __DIR__.'/config/optimus.components.php');
        }
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $config = $this->app['config']['optimus.components'];

        $middleware = $config['protection_middleware'];
        $highLevelParts = array_map(function ($namespace) {
            return glob(sprintf('%s%s*', $namespace, DIRECTORY_SEPARATOR), GLOB_ONLYDIR);
        }, $config['namespaces']);

        foreach ($highLevelParts as $part => $partComponents) {
            foreach ($partComponents as $componentRoot) {
                $component = substr($componentRoot, strrpos($componentRoot, '/')+1);
                $namespace = $part . '\\' . $component . '\\Controllers';

                $fileNames = [
                    'routes' => true,
                    'routes_protected' => true,
                    'routes_public' => false
                ];

                foreach ($fileNames as $fileName => $protected) {
                    $path = sprintf('%s/%s.php', $componentRoot, $fileName);

                    if (file_exists($path)) {
                        $router->group([
                            'middleware' => $protected ? $middleware : [],
                            'namespace'  => $namespace
                        ], function ($router) use ($path) {
                            require $path;
                        });
                    }
                }
            }
        }
    }
}

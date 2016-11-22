<?php

namespace Optimus\Api\System;

use Illuminate\Routing\Router;
use Optimus\Api\System\Options\Config;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
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

    /**
     * Register
     */
    public function register()
    {
        $this->registerAssets();
    }

    /**
     * Register assets
     */
    private function registerAssets()
    {
        $this->publishes([
            __DIR__ . '/config/optimus.components.php' => config_path('optimus.components.php'),
        ]);
    }

    /**
     * Load configuration
     */
    private function loadConfig()
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app['config'];

        if ($config->get('optimus.components') !== null) {
            return;
        }

        $config->set('optimus.components', require __DIR__ . '/config/optimus.components.php');
    }
    /**
     * Define the routes for the application.
     *
     * @param Router $router
     */
    public function map(Router $router)
    {
        $config = $this->getConfig();

        $middleware = $config->get('middleware');
        $protectionMiddleware = $config->get('protection_middleware');

        $highLevelParts = array_map(function ($namespace) {
            return glob(sprintf('%s%s*', $namespace, DIRECTORY_SEPARATOR), GLOB_ONLYDIR);
        }, $config->get('namespaces'));

        foreach ($highLevelParts as $part => $partComponents) {
            foreach ($partComponents as $componentRoot) {
                $component = substr($componentRoot, strrpos($componentRoot, '/') + 1);

                $namespace = sprintf(
                    '%s\\%s\\Controllers',
                    $part,
                    $component
                );

                $fileNames = [
                    'routes' => true,
                    'routes_protected' => true,
                    'routes_public' => false,
                ];

                foreach ($fileNames as $fileName => $protected) {
                    $path = sprintf('%s/%s.php', $componentRoot, $fileName);

                    if (!file_exists($path)) {
                        continue;
                    }

                    $router->group([
                        'middleware' => $protected ? $protectionMiddleware : $middleware,
                        'namespace'  => $namespace,
                    ], function ($router) use ($path) {
                        require $path;
                    });
                }
            }
        }
    }

    /**
     * Module configuration
     *
     * @return Config
     */
    public function getConfig()
    {
        return new Config($this->app['config']['optimus.components']);
    }
}

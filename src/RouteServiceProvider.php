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

        $this->booted(function () {
            $this->loadRoutes();
        });
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

        if ($config->get('optimus.components') === null) {
            $config->set('optimus.components', require __DIR__ . '/config/optimus.components.php');
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

        $protectionMiddleware = $config['protection_middleware'];

        $highLevelParts = array_map(function ($namespace) {
            if (! is_array($namespace)) {
                $namespace = [
                    'path' => $namespace,
                    'route' => []
                ];
            }

            if (! array_key_exists('route', $namespace)) {
                $namespace['route'] = [];
            }

            if (! array_key_exists('middleware', $namespace['route'])) {
                $namespace['route']['middleware'] = [];
            }

            return [
                'components' => glob(sprintf('%s%s*', $namespace['path'], DIRECTORY_SEPARATOR), GLOB_ONLYDIR),
                'route'      => $namespace['route'],
            ];
        }, $config['namespaces']);

        foreach ($highLevelParts as $highPart => $part) {
            foreach ($part['components'] as $componentRoot) {
                $component = substr($componentRoot, strrpos($componentRoot, DIRECTORY_SEPARATOR) + 1);

                $namespace = sprintf(
                    '%s\\%s\\Controllers',
                    $highPart,
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

                    $middleware = $part['route']['middleware'];
                    if ($protected) {
                        $middleware = array_merge($protectionMiddleware, $middleware);
                    }

                    $group = array_merge($part['route'], [
                        'namespace'  => $namespace,
                        'middleware' => $middleware,
                    ]);

                    $router->group($group, function ($router) use ($path) {
                        require $path;
                    });
                }
            }
        }
    }
}

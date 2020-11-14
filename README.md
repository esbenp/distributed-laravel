# Distributed Laravel

[![Build Status](https://travis-ci.org/esbenp/distributed-laravel.svg)](https://travis-ci.org/esbenp/distributed-laravel) [![Coverage Status](https://coveralls.io/repos/esbenp/distributed-laravel/badge.svg?branch=master)](https://coveralls.io/r/esbenp/distributed-laravel?branch=master)

**Use 0.1.1 for Laravel 5.2 compatibility**

Some service providers to enable a Laravel project structure that is grouped by components rather than class types.

## Installation

```bash
composer require optimus/distributed-laravel 0.1.*
```

## Usage
Define a `optimus.components.php` configuration file. For example:

```php
<?php

return [
    'namespaces' => [
        // Define a simple namespace mapping
        'Infrastructure' => base_path() . DIRECTORY_SEPARATOR . 'infrastructure',

        // Here we define a namespace mapping with route config
        'Api' => [
            'path' => base_path() . DIRECTORY_SEPARATOR . 'api',
            'route' => [
                'middleware' => [
                    'requestid'
                ]
            ]
        ],
    ],

    // middleware to be applied to all routes within routes.php or routes_protected.php.
    'protection_middleware' => [
        'auth:api'
    ],

    'resource_namespace' => 'resources',

    'language_folder_name' => 'lang',

    'view_folder_name' => 'views'
];
```

<?php

namespace Optimus\Api\System\Options;

class Config extends Options
{
    /**
     * Defaults
     *
     * @var array
     */
    protected $defaults = [
        'namespaces' => [],

        'middleware' => [],

        'protection_middleware' => [],

        'resource_namespace' => 'resources',

        'language_folder_name' => 'lang',

        'view_folder_name' => 'views',
    ];
}

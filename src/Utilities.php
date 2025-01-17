<?php

namespace Optimus\Api\System;

class Utilities
{
    public static function findNamespaceResources(array $namespaces, $resourceFolderName, $resourceNamespace)
    {
        return array_reduce($namespaces, function ($carry, $namespaceConfig) use ($resourceNamespace, $resourceFolderName) {
            if (! is_array($namespaceConfig)) {
                $namespaceConfig = ['path' => $namespaceConfig];
            }

            $components = glob(sprintf('%s%s*', $namespaceConfig['path'], DIRECTORY_SEPARATOR), GLOB_ONLYDIR);

            $paths = array_map(function ($component) use ($resourceNamespace, $resourceFolderName) {
                $path = [$component];

                if (!empty($resourceNamespace)) {
                    $path[] = $resourceNamespace;
                }

                $path[] = $resourceFolderName;

                $path = implode(DIRECTORY_SEPARATOR, $path);

                return is_dir($path) ? $path : false;
            }, $components);

            return array_merge($carry, array_filter($paths));
        }, []);
    }
}

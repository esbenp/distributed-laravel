<?php

namespace Optimus\Api\System;

class Utilities
{
    public static function findNamespaceResources(array $namespaces, $resourceFolderName, $resourceNamespace)
    {
        return array_reduce($namespaces, function ($carry, $namespacePath) use ($resourceNamespace, $resourceFolderName) {
            $components = glob(sprintf('%s%s*', $namespacePath, DIRECTORY_SEPARATOR), GLOB_ONLYDIR);

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

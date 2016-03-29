<?php

namespace Optimus\Api\System\Translation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;

class DistributedFileLoader extends FileLoader
{
    protected $paths = [];

    /**
     * Create a new file loader instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $path
     * @return void
     */
    public function __construct(Filesystem $files, array $paths = [])
    {
        $this->paths = $paths;
        $this->files = $files;
    }

    /**
     * Load a locale from a given path.
     *
     * @param  string  $path
     * @param  string  $locale
     * @param  string  $group
     * @return array
     */
    protected function loadPath($path, $locale, $group)
    {
        $result = [];
        foreach ($this->paths as $path) {
            $result = array_merge($result, parent::loadPath($path, $locale, $group));
        }

        return $result;
    }
}

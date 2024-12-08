<?php

namespace App\Configuration\Building;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class BuildingConfigLoader extends FileLoader
{

    public function load(mixed $resource, ?string $type = null): mixed
    {
        return Yaml::parse(file_get_contents($resource));
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return is_string($resource) && 'yaml' == pathinfo(
                $resource,
                PATHINFO_EXTENSION
            );
    }
}
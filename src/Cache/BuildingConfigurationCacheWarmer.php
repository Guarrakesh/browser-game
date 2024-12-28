<?php

namespace App\Cache;

use App\DependencyInjection\GameConfiguration;
use Symfony\Component\Config\Builder\ConfigBuilderGenerator;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class BuildingConfigurationCacheWarmer implements CacheWarmerInterface
{

    public function isOptional(): bool
    {
        return false;
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        if (!$buildDir) {
            return [];
        }

            $generator = new ConfigBuilderGenerator($buildDir);
        $generator->build(new GameConfiguration());

        return [];
    }
}
<?php

namespace App\Service;

class BuildingConfigurationService
{
    public function __construct(private readonly array $config)
    {
    }

    public function getBuildingInfo(string $name): ?array
    {
        return $this->config['buildings'][$name] ?? null;
    }

    public function getStartupBuildingConfig()
    {
        $buildingConfigs = [];
        foreach ($this->config['buildings'] as $name => $buildingConfig) {
            if ($buildingConfig['min_level'] > 0) {
                $buildingConfigs[$name] = $buildingConfig;
            }
        }

        return $buildingConfigs;
    }


}
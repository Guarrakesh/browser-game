<?php

namespace App\Security;

class BuildingConfigurationService
{
    public function __construct(private readonly array $config)
    {
    }

    public function getBuildingInfo(string $name): ?array
    {
        return $this->config['buildings'][$name] ?? null;
    }


}
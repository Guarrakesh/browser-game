<?php

namespace App\Planet\GameObject\Building;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class PowerBuildingDefinition extends BuildingDefinition
{



    public function getEnergyYieldIncreaseFactor(): float
    {
        return $this->config['energy_yield_increase_factor'];
    }

    public function getBaseEnergyYield(): int
    {
        return $this->config['energy_base_yield'];
    }

}
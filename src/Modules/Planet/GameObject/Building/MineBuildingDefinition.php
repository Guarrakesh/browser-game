<?php

namespace App\Modules\Planet\GameObject\Building;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class MineBuildingDefinition extends BuildingDefinition
{



    public function getProdIncreaseFactor(): float
    {
        return $this->config['production_increase_factor'];
    }

    public function getBaseDroneSlots(): int
    {
        return $this->config['drone_slots']['base'];
    }

    public function getDroneSlotsIncreasePerLevel(): int
    {
        return $this->config['drone_slots']['per_level'];
    }

    public function getDroneProdMultiplier(): float
    {
        return $this->config['drone_slots']['prod_multiplier'];
    }
}
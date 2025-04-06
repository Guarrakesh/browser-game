<?php

namespace App\Modules\Planet\Dto;


use App\Modules\Planet\GameObject\Building\MineBuildingDefinition;
use App\Modules\Shared\Dto\GameObject;
use App\Modules\Shared\Dto\GameObjectLevel;

class PlanetMineGameObjectDTO extends GameObjectLevel
{
    public function __construct(
        GameObject             $object,
        int                    $level,
        MineBuildingDefinition $definition,
        private readonly float $production = 0.0,
        private readonly int $allocatedDrones = 0,
        private readonly ?PlanetMineGameObjectDTO $nextLevelMine = null,



    )
    {
        parent::__construct($object, $level, $definition);
//        $this->definition->getEnergyConsumptionAtLevel($this->level),
//            $this->definition->getBaseDroneSlots(),
//            $this->droneAllocation?->getAmount() ?? 0,
//            $this->getProduction(),
//            $this->definition->getDroneSlotsIncreasePerLevel(),
//            $this->definition->getDroneProdMultiplier(),
    }


    public function getDefinition(): MineBuildingDefinition
    {
        /** @var MineBuildingDefinition $definition */
        $definition = parent::getDefinition();

        return $definition;
    }

    public function getDroneSlots(): int
    {
        $definition = $this->getDefinition();
        return $definition->getBaseDroneSlots() + $definition->getDroneSlotsIncreasePerLevel() * $this->getLevel();
    }

    public function getAllocatedDrones(): int
    {
        return $this->allocatedDrones;
    }

    public function getProduction(): float
    {
        return $this->production;
    }

    public function getDroneMultiplier(): float
    {
        return $this->getDefinition()->getDroneProdMultiplier();
    }

    public function getDroneIncreasePerLevel(): int
    {
        return $this->getDefinition()->getDroneSlotsIncreasePerLevel();
    }

    public function getNextLevelMine(): ?PlanetMineGameObjectDTO {
        return $this->nextLevelMine;
    }


}

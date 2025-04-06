<?php

namespace App\Modules\Planet\Model;

use App\Modules\Planet\Dto\PlanetMineGameObjectDTO;
use App\Modules\Planet\GameObject\Building\MineBuildingDefinition;
use App\Modules\Planet\Model\Entity\Drone\DroneAllocation;
use App\Modules\Shared\Dto\GameObject;
use App\Modules\Shared\Model\ObjectType;

readonly class ProductionMine
{
    public function __construct(private MineBuildingDefinition $definition, private string $name, private int $level, private ?DroneAllocation $droneAllocation = null)
    {
    }

    public function getProduction(?int $level = null): float
    {
        $level ??= $this->level;

        $prodIncreaseFactor = $this->definition->getProdIncreaseFactor();
        $dronesFactor = $this->droneAllocation ? $this->getDronesFactor() : 1;

        return $this->definition->getHourlyProduction() * ($prodIncreaseFactor ** ($level - 1)) * $dronesFactor;
    }


    /**
     * Returns the number of drones allocated to the building, multiplied by the drone multiplied
     */
    public function getDronesFactor(): float
    {
        if (!$this->droneAllocation) {
            return 1;
        }
        return $this->droneAllocation->getAmount() * $this->definition->getDroneProdMultiplier();
    }

    public function getBuildingName(): ?string
    {
        return $this->name;
    }

    public function getAsMineGameObject(): PlanetMineGameObjectDTO
    {
        $nextLevelMine = null;
        if ($this->level + 1 <= $this->definition->getMaxLevel()) {
            $nextLevelMine = new PlanetMineGameObjectDTO(
                new GameObject($this->name, ObjectType::Building),
                $this->level + 1,
                $this->definition,
                $this->getProduction($this->level + 1),
                $this->droneAllocation?->getAmount() ?? 0,
            );
        }


        return new PlanetMineGameObjectDTO(
            new GameObject($this->name, ObjectType::Building),
            $this->level,
            $this->definition,
            $this->getProduction(),
            $this->droneAllocation?->getAmount() ?? 0,
            $nextLevelMine
        );
    }




    public function getLevel(): int
    {
        return $this->level;
    }

    public function getDroneAllocation(): ?DroneAllocation
    {
        return $this->droneAllocation;
    }



}
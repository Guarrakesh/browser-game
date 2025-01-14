<?php

namespace App\Model\Building;

readonly class BuildingRequirement
{
    /**
     * @param array<string,int> $requiredBuildings
     */
    public function __construct(private array $requiredBuildings)
    {}

    public function isSatisfied(CampBuildingList $buildingList): bool
    {
        foreach ($this->requiredBuildings as $building => $level) {
            if (!$buildingList->hasBuilding($building)
                || $buildingList->getBuildingLevel($building) < $level) {
                return false;
            }
        }

        return true;
    }

    public function getRequiredBuildings(): array
    {
        return $this->requiredBuildings;
    }
}
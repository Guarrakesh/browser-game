<?php

namespace App\Model;

// TODO: Cleanup, make it POPO
readonly class BuildingRequirements
{
    /**
     * @param array<string,int> $requiredBuildings
     * TODO: Convert to a "ObjectRequirementDTO"
     */
    public function __construct(private array $requiredBuildings)
    {}

    public function isSatisfied(PlanetBuildingList $buildingList): bool
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
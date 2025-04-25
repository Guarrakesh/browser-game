<?php

namespace App\Planet;

use App\Planet\Domain\Entity\Planet;
use App\Planet\Domain\ValueObject\Location;
use App\Planet\Service\BuildingRegistry;
use App\Shared\Dto\GameObjectLevel;
use App\Shared\Model\ResourcePack;

class PlanetFactory
{
    public function __construct(
        private readonly BuildingRegistry $buildingRegistry,
    )
    {
    }

    /**
     * @param GameObjectLevel[] $buildingList
     */
    public function createNewPlanet(int $playerId,
                                    string $name,
                                    array $buildingList,
                                    Location $location,
                                    ?ResourcePack $initialResources = null
    ): Planet
    {

        $planet = new Planet($playerId, $name, $location, 100);
        foreach ($buildingList as $building) {
            $planet->upgradeBuilding($this->buildingRegistry->get($building->getObject()->getName()), $building->getLevel());
        }

        $pack = $initialResources ?? new ResourcePack(100, 100, 100, 100);
        $planet->creditResources($pack);

        return $planet;
    }
}
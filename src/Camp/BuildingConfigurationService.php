<?php

namespace App\Camp;

use App\Camp\Building\Building;
use App\Camp\Building\BuildingInterface;
use App\Model\Building\CampBuildingList;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

readonly class BuildingConfigurationService
{
    /**
     * @param ServiceLocator<BuildingInterface> $buildingConfigs
     */
    public function __construct(
        #[AutowireLocator(BuildingInterface::class, indexAttribute: 'key')] private ServiceLocator $buildingConfigs
    )
    {}

    public function getBuildingConfigProvider(string $name): Building
    {
        return $this->buildingConfigs->get($name);
    }

    /**
     * @throws Exception
     */
    public function getStartupBuildingConfig(): CampBuildingList
    {
        $buildingList = new CampBuildingList();

        foreach ($this->buildingConfigs->getIterator() as $provider) {
            /** @var BuildingInterface $provider */
            //if ($provider->getRequirements()->isSatisfied())
            if ($provider->getMinLevel() > 0) {
                $buildingList->addBuilding($provider->getName(), $provider->getMinLevel());
            }
        }

        return $buildingList;
    }

    /**
     * @return array<Building>
     * @throws Exception
     */
    public function getAllConfigs(): array
    {
        $buildings = [];

        foreach ($this->buildingConfigs->getIterator() as $provider) {
            /** @var BuildingInterface $provider */
            $buildings[] = $provider;
        }
        return $buildings;
    }

}
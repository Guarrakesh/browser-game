<?php

namespace App\ObjectRegistry;

use App\Model\CampBuildingList;
use App\ObjectDefinition\Building\BuildingDefinition;
use App\ObjectDefinition\Building\BuildingDefinitionInterface;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

readonly class BuildingRegistry
{
    /**
     * @param ServiceLocator<BuildingDefinitionInterface> $buildingConfigs
     */
    public function __construct(
        #[AutowireLocator(BuildingDefinitionInterface::class, indexAttribute: 'key')] private ServiceLocator $buildingConfigs
    )
    {}

    public function getBuildingConfigProvider(string $name): BuildingDefinition
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
            /** @var BuildingDefinitionInterface $provider */
            //if ($provider->getRequirements()->isSatisfied())
            if ($provider->getMinLevel() > 0) {
                $buildingList->addBuilding($provider->getName(), $provider->getMinLevel());
            }
        }

        return $buildingList;
    }

    /**
     * @return array<BuildingDefinition>
     * @throws Exception
     */
    public function getAllConfigs(): array
    {
        $buildings = [];

        foreach ($this->buildingConfigs->getIterator() as $provider) {
            /** @var BuildingDefinitionInterface $provider */
            $buildings[] = $provider;
        }
        return $buildings;
    }

}
<?php

namespace App\ObjectRegistry;

use App\Exception\GameObjectNotFoundException;
use App\Model\PlanetBuildingList;
use App\ObjectDefinition\Building\BuildingDefinition;
use App\ObjectDefinition\Building\BuildingDefinitionInterface;
use App\ObjectDefinition\ObjectType;
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

    public function find(string $name): ?BuildingDefinitionInterface
    {
        return $this->buildingConfigs->has($name) ? $this->buildingConfigs->get($name) : null;
    }
    public function get(string $name): BuildingDefinition
    {
        if (!$this->buildingConfigs->has($name)) {
            throw new GameObjectNotFoundException(ObjectType::Building, $name);
        }

        return $this->buildingConfigs->get($name);
    }

    /**
     * @throws Exception
     */
    public function getStartupBuildingConfig(): PlanetBuildingList
    {
        $buildingList = new PlanetBuildingList();

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
    public function getAll(): array
    {
        $buildings = [];

        foreach ($this->buildingConfigs->getIterator() as $provider) {
            /** @var BuildingDefinitionInterface $provider */
            $buildings[] = $provider;
        }
        return $buildings;
    }

    /**
     * @return iterable<BuildingDefinition>
     */
    public function getIterator(): iterable
    {
        return $this->buildingConfigs->getIterator();
    }
}
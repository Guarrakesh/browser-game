<?php

namespace App\Modules\Planet\Service;

use App\Exception\GameObjectNotFoundException;
use App\Modules\Planet\GameObject\Building\BuildingDefinition;
use App\Modules\Planet\GameObject\Building\BuildingDefinitionInterface;
use App\Modules\Shared\Dto\GameObject;
use App\Modules\Shared\Dto\GameObjectLevel;
use App\Modules\Shared\Model\ObjectType;
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
    {
    }

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
     * @return GameObjectLevel[]
     */
    public function getStartupBuildingConfig(): array
    {
        $buildingList = [];
        try {
            foreach ($this->buildingConfigs->getIterator() as $buildingDefinition) {
                /** @var BuildingDefinitionInterface $buildingDefinition */
                //if ($buildingDefinition->getRequirements()->isSatisfied())
                if ($buildingDefinition->getMinLevel() > 0) {
                    $object = new GameObject($buildingDefinition->getName(), ObjectType::Building);
                    $buildingList[$buildingDefinition->getName()] = new GameObjectLevel($object, $buildingDefinition->getMinLevel());
                }
            }
        } catch (Exception $exception) {
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
<?php

namespace App\Modules\Planet\Dto\ObjectDefinition\Building;

use App\Modules\Planet\Dto\ObjectDefinition\AbstractDefinition;
use App\Modules\Shared\Dto\GameObject;
use App\Modules\Shared\Dto\GameObjectLevel;
use App\Modules\Shared\Model\ObjectType;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class BuildingDefinition extends AbstractDefinition implements BuildingDefinitionInterface
{
    /** @var GameObjectLevel[] */
    private ?array $_buildingRequirement = null;

    public function getConfig(string $name): mixed
    {
        return $this->config[$name] ?? null;
    }

    public function getBasePopulation(): ?int
    {
        return $this->config['base_population'] ?? null;
    }

    public function getHourlyProduction(): ?int
    {
        return $this->config['base_hourly_production'] ?? null;
    }

    public function getIncreaseFactor(): ?float
    {
        return $this->config['increase_factor'] ?? 1.0;
    }


    /** {@inheritDoc} */
    public function getRequirements(): array
    {
        if ($this->_buildingRequirement === null) {
            $this->_buildingRequirement = [];
            $requires = $this->config['requires'];
            foreach ($requires as $type => $requirements) {
                foreach ($requirements as $objectName => $level) {
                    $this->_buildingRequirement[] = new GameObjectLevel(
                        new GameObject($objectName, ObjectType::fromConfigLabel($type)),
                        $level
                    );
                }
            }
        }

        return $this->_buildingRequirement;
    }


    public function getMinLevel(): ?int
    {
        return $this->config['min_level'] ?? null;
    }

    public function getMaxLevel(): ?int
    {
       return $this->config['max_level'] ?? null;
    }


    public function getType(): ObjectType
    {
        return ObjectType::Building;
    }



}
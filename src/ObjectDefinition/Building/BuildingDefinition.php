<?php

namespace App\ObjectDefinition\Building;

use App\Model\BuildingRequirements;
use App\Model\PlanetBuildingList;
use App\Modules\Core\Entity\Planet;
use App\ObjectDefinition\AbstractDefinition;
use App\ObjectDefinition\ObjectType;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class BuildingDefinition extends AbstractDefinition implements BuildingDefinitionInterface
{
    private ?BuildingRequirements $_buildingRequirement = null;

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
    public function getRequirements(): BuildingRequirements
    {
        if ($this->_buildingRequirement === null) {
            $requires = $this->config['requires'];

            $this->_buildingRequirement = new BuildingRequirements($requires);
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


    public function getLevel(Planet $planet): ?int
    {
        return $planet->getBuilding($this->name)?->getLevel();
    }

    public function getType(): ObjectType
    {
        return ObjectType::Building;
    }


}
<?php

namespace App\ObjectDefinition\Building;

use App\CurveCalculator\CalculatorConfig;
use App\Entity\World\Camp;
use App\Model\Building\BuildingRequirement;
use App\Model\Building\CampBuildingList;
use App\Model\ResourcePack;
use App\ObjectDefinition\AbstractDefinition;
use LogicException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class BuildingDefinition extends AbstractDefinition implements BuildingDefinitionInterface
{

    private ?ResourcePack $_baseCost = null;
    private ?BuildingRequirement $_buildingRequirement = null;


    public function getConfig(string $name): mixed
    {
        return $this->config[$name] ?? null;
    }

    public function getCostFactor(): ?float
    {
        return $this->config['cost_factor'] ?? null;
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

    public function getCalculatorConfig(string $name): CalculatorConfig
    {
        if (isset($this->config[$name]['parameters']) && isset($this->config[$name]['service'])) {
            return new CalculatorConfig($this->config[$name]['service'], $this->config[$name]['parameters']);
        }

        throw new LogicException(sprintf("Expected to find %s config in building %s", $name, $this->getName()));
    }

    /** {@inheritDoc} */
    public function getBaseCost(): ResourcePack
    {
        if ($this->_baseCost === null) {
            $baseCost = $this->config['base_cost'];

            $this->_baseCost = new ResourcePack(
                $baseCost['concrete'] ?? 0,
                $baseCost['metals'] ?? 0,
                $baseCost['circuits'] ?? 0,
                $baseCost['food'] ?? 0
            );
        }

        return $this->_baseCost;
    }

    /** {@inheritDoc} */
    public function getBaseBuildTime(): int
    {
        return $this->config['base_build_time'];
    }


    /** {@inheritDoc} */
    public function getRequirements(): BuildingRequirement
    {
        if ($this->_buildingRequirement === null) {
            $requires = $this->config['requires'];

            $this->_buildingRequirement = new BuildingRequirement($requires);
        }

        return $this->_buildingRequirement;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMinLevel(): ?int
    {
        return $this->config['min_level'] ?? null;
    }

    public function getMaxLevel(): ?int
    {
       return $this->config['max_level'] ?? null;
    }

    public function areRequirementsSatisfied(Camp|CampBuildingList $value): bool
    {
        if ($value instanceof CampBuildingList) {
            return $this->getRequirements()->isSatisfied($value);
        }

        return $this->getRequirements()->isSatisfied(CampBuildingList::fromCamp($value));
    }

    public function getLevel(Camp $camp): ?int
    {
        return $camp->getBuilding($this->name)?->getLevel();
    }
}
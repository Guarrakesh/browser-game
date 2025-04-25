<?php

namespace App\Planet\GameObject\Building;

use App\Shared\Dto\GameObject;
use App\Shared\Dto\GameObjectLevel;
use App\Shared\GameObject\AbstractDefinition;
use App\Shared\Model\ObjectType;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class BuildingDefinition extends AbstractDefinition implements BuildingDefinitionInterface
{
    /** @var GameObjectLevel[] */
    private ?array $_buildingRequirement = null;

    public function getConfig(string $name): mixed
    {
        return $this->config[$name] ?? null;
    }


    public function getHourlyProduction(): ?int
    {
        return $this->config['base_hourly_production'] ?? null;
    }

    public function getIncreaseFactor(): ?float
    {
        return $this->config['production_increase_factor'] ?? 1.0;
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
                        $level,
                        $this
                    );
                }
            }
        }

        return $this->_buildingRequirement;
    }


    public function getMinLevel(): ?int
    {
        return $this->config['min_level'] ?? 0;
    }

    public function getMaxLevel(): ?int
    {
        return $this->config['max_level'] ?? 0;
    }


    public function getType(): ObjectType
    {
        return ObjectType::Building;
    }


    public function getBaseEnergyConsumption(): int
    {
        return $this->config['energy_base_consumption'] ?? 0;
    }

    public function getEnergyConsumptionIncreaseFactor(): float
    {
        return $this->config['energy_consumption_increase_factor'] ?? 1.0;
    }

    public function getEnergyIncreaseAtLevel(int $level): int
    {
        if ($level < 1) {
            return 0;
        }

        $previousLevelEnergy = ($level-2 < 1) ? 0 : round($this->getBaseEnergyConsumption() * ($this->getEnergyConsumptionIncreaseFactor() ** ($level - 2)));

        return round($this->getBaseEnergyConsumption() * ($this->getEnergyConsumptionIncreaseFactor() ** ($level - 1)))
            - $previousLevelEnergy;
    }

    public function getTotalEnergyAtLevel(int $level): int
    {
        return round($this->getBaseEnergyConsumption() * ($this->getEnergyConsumptionIncreaseFactor() ** ($level - 1)));
    }

}
<?php

namespace App\Planet\Service;

use App\Shared\Constants;
use App\Shared\Model\ResourcePack;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[Exclude]
readonly class DroneConfigurationService
{
    private ResourcePack $cost;
    private float $energy;
    private float $baseBuildTime;

    private float $costFactor;

    private int $maxPlanetDrones;
    public function __construct(
        array $config = []
    )
    {
        $this->resolveConfig($config);
        $this->cost = new ResourcePack(
            $config['base_cost']['concrete'] ?? 0,
            $config['base_cost']['metals'] ?? 0,
            $config['base_cost']['polymers'] ?? 0,
            $config['base_cost']['food'] ?? 0
        );
        $this->energy = $config['energy'];
        $this->maxPlanetDrones = $config['max_planet_drones'];
        $this->costFactor = $config['cost_factor'];
    }

    public function getCost(int $existingDrones): ResourcePack
    {
        return $this->cost->power($this->costFactor, $existingDrones);
    }

    public function getEnergyConsumption(): float
    {
        return $this->energy;
    }

    public function getMaxPlanetDrones(): int
    {
        return $this->maxPlanetDrones;
    }


    public function getCostFactor(): float
    {
        return $this->costFactor;
    }

    public function getBaseBuildTime(): float
    {
        return $this->baseBuildTime;
    }



    private function resolveConfig(array $config): void
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['energy', 'cost', 'max_planet_drones', 'cost_factor']);
        $resolver->setAllowedTypes('energy', 'numeric');
        $resolver->setAllowedTypes('cost', 'array');
        $resolver->setAllowedTypes('max_planet_drones', 'numeric');
        $resolver->setAllowedTypes('cost_factor', 'numeric');
        $resolver->setDefault('cost', function (OptionsResolver $costResolver): void {
           $costResolver->setDefaults([
               Constants::CONCRETE => 0,
               Constants::METALS => 0,
               Constants::POLYMERS => 0,
               Constants::FOOD => 0
           ]);
        });


    }

}
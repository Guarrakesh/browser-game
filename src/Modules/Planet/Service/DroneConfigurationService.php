<?php

namespace App\Modules\Planet\Service;

use App\Modules\Shared\Constants;
use App\Modules\Shared\Model\ResourcePack;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[Exclude]
readonly class DroneConfigurationService
{
    private ResourcePack $cost;
    private float $energy;
    private float $baseBuildTime;

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
    }

    public function getCost(int $existingDrones): ResourcePack
    {
        return $this->cost->multiply(1 + $existingDrones);
    }

    public function getEnergyConsumption(): float
    {
        return $this->energy;
    }



    private function resolveConfig(array $config): void
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['energy', 'cost']);
        $resolver->setAllowedTypes('energy', 'numeric');
        $resolver->setAllowedTypes('cost', 'array');
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
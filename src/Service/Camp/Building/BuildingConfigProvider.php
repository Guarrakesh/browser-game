<?php

namespace App\Service\Camp\Building;

use App\Model\Building\BuildingRequirement;
use App\Model\ResourcePack;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[Exclude]
final class BuildingConfigProvider implements BuildingConfigProviderInterface
{
    private readonly array $config;

    private ?ResourcePack $_baseCost = null;
    private ?BuildingRequirement $_buildingRequirement = null;


    public function __construct(private readonly string $name, array $config)
    {
        $this->resolveConfig($config);

        $this->config = $config;
    }

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

    /** {@inheritDoc} */
    public function getBaseCost(): ResourcePack
    {
        if ($this->_baseCost == null) {
            $baseCost = $this->config['base_cost'];

            $this->_baseCost = new ResourcePack(
                $baseCost['concrete'],
                $baseCost['metals'],
                $baseCost['circuits'],
                $baseCost['food']
            );
        }

        return $this->_baseCost;
    }


    /** {@inheritDoc} */
    public function getRequirements(): BuildingRequirement
    {
        if ($this->_buildingRequirement == null) {
            $requires = $this->config['requires'];

            $this->_buildingRequirement = new BuildingRequirement($requires);
        }

        return $this->_buildingRequirement;
    }

    private function resolveConfig(array $config): void
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired([
            'base_cost',
            'base_population',
            'cost_factor',
            'requires',
            'min_level',
            'max_level'
        ]);

        $resolver->resolve($config);
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
}
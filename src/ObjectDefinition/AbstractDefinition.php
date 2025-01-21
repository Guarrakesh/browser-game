<?php

namespace App\ObjectDefinition;

use App\CurveCalculator\CalculatorConfig;
use App\Object\ResourcePack;
use LogicException;

class AbstractDefinition implements BaseDefinitionInterface
{

    private ?ResourcePack $_baseCost = null;

    protected readonly array $config;

    public function __construct(protected string $name, array $config)
    {
        $this->config = $config;
    }

    public function getConfig(string $name): mixed
    {
        return $this->config[$name] ?? null;
    }

    public function getName(): string
    {
        return $this->name;
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

}
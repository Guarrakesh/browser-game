<?php

namespace App\Ship;

readonly class ShipDefinition implements ShipDefinitionInterface
{
    private array $config;

    public function __construct(private string $name, array $config)
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
}
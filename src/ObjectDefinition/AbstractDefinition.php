<?php

namespace App\ObjectDefinition;

class AbstractDefinition implements BaseDefinitionInterface
{
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
}
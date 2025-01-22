<?php

namespace App\ObjectDefinition;

use App\Object\ResourcePack;

interface BaseDefinitionInterface
{
    public function getConfig(string $name): mixed;
    public function getName(): string;

    public function getBaseCost(): ResourcePack;

    public function getType(): ObjectType;
    public function getParameters(): array;
    public function findParameter(string $name): mixed;
}
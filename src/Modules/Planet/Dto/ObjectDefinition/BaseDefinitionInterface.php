<?php

namespace App\Modules\Planet\Dto\ObjectDefinition;

use App\Modules\Shared\Model\ObjectType;
use App\Modules\Shared\Model\ResourcePack;

interface BaseDefinitionInterface
{
    public function getConfig(string $name): mixed;
    public function getName(): string;

    public function getBaseCost(): ResourcePack;

    public function getType(): ObjectType;
    public function getParameters(): array;
    public function findParameter(string $name): mixed;
}
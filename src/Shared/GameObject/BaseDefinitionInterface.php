<?php

namespace App\Shared\GameObject;

use App\Shared\Dto\GameObject;
use App\Shared\Dto\GameObjectLevel;
use App\Shared\Model\ObjectType;
use App\Shared\Model\ResourcePack;

interface BaseDefinitionInterface
{
    public function getConfig(string $name): mixed;
    public function getName(): string;

    public function getBaseCost(): ResourcePack;

    public function getType(): ObjectType;
    public function getParameters(): array;
    public function findParameter(string $name): mixed;

    /** @return array<GameObjectLevel> */
    public function getRequirements(): array;

    public function getAsGameObject(): GameObject;


}
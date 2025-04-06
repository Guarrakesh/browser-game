<?php

namespace App\Modules\Shared\GameObject;

use App\Modules\Shared\Dto\GameObject;
use App\Modules\Shared\Dto\GameObjectLevel;
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

    /** @return array<GameObjectLevel> */
    public function getRequirements(): array;

    public function getAsGameObject(): GameObject;


}
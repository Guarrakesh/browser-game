<?php

namespace App\Modules\Planet\Dto\ObjectDefinition\Building;

use App\Modules\Planet\Dto\GameObjectLevel;
use App\Modules\Planet\Dto\ObjectDefinition\BaseDefinitionInterface;
use App\Modules\Shared\Model\ResourcePack;

interface BuildingDefinitionInterface extends BaseDefinitionInterface
{
    /**
     * @return string Get the name of the building
     */
    public function getName(): string;
    public function getConfig(string $name): mixed;


    public function getBasePopulation(): ?int;

    /**
     * @return ResourcePack The pack of resources required.
     */
    public function getBaseCost(): ResourcePack;

    /**
     * @return GameObjectLevel[] The required building and levels.
     */
    public function getRequirements(): array;

    public function getMinLevel(): ?int;
    public function getMaxLevel(): ?int;
}
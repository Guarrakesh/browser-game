<?php

namespace App\ObjectDefinition\Building;

use App\Model\BuildingRequirements;
use App\Object\ResourcePack;
use App\ObjectDefinition\BaseDefinitionInterface;

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
     * @return BuildingRequirements The required building and levels.
     */
    public function getRequirements(): BuildingRequirements;

    public function getMinLevel(): ?int;
    public function getMaxLevel(): ?int;
}
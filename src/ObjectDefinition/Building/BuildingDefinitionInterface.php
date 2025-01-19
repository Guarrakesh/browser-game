<?php

namespace App\ObjectDefinition\Building;

use App\Model\BuildingRequirement;
use App\Object\ResourcePack;
use App\ObjectDefinition\BaseDefinitionInterface;

interface BuildingDefinitionInterface extends BaseDefinitionInterface
{
    /**
     * @return string Get the name of the building
     */
    public function getName(): string;
    public function getConfig(string $name): mixed;

    public function getCostFactor(): ?float;

    public function getBasePopulation(): ?int;

    /**
     * @return int|null Base Build time in seconds
     */
    public function getBaseBuildTime(): ?int;

    /**
     * @return ResourcePack The pack of resources required.
     */
    public function getBaseCost(): ResourcePack;

    /**
     * @return BuildingRequirement The required building and levels.
     */
    public function getRequirements(): BuildingRequirement;

    public function getMinLevel(): ?int;
    public function getMaxLevel(): ?int;
}
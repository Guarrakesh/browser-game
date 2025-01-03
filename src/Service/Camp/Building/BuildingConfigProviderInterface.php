<?php

namespace App\Service\Camp\Building;

use App\Model\Building\BuildingRequirement;
use App\Model\ResourcePack;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

interface BuildingConfigProviderInterface
{
    /**
     * @return string Get the name of the building
     */
    public function getName(): string;
    public function getConfig(string $name): mixed;

    public function getCostFactor(): ?float;

    public function getBasePopulation(): ?int;

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
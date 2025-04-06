<?php

namespace App\Modules\Planet\GameObject\Building;

use App\Modules\Shared\Dto\GameObjectLevel;
use App\Modules\Shared\GameObject\BaseDefinitionInterface;
use App\Modules\Shared\Model\ResourcePack;

interface BuildingDefinitionInterface extends BaseDefinitionInterface
{
    /**
     * @return string Get the name of the building
     */
    public function getName(): string;
    public function getConfig(string $name): mixed;


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



    public function getBaseEnergyConsumption();

    public function getEnergyConsumptionIncreaseFactor();

    public function getEnergyConsumptionAtLevel(int $level);


}
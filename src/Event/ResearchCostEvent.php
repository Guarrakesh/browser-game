<?php

namespace App\Event;

use App\Entity\World\Camp;
use App\Entity\World\Player;
use App\Object\ResourcePack;
use App\ObjectDefinition\Building\BuildingDefinitionInterface;
use App\ObjectDefinition\Research\ResearchTechDefinitionInterface;

class ResearchCostEvent extends GameEvent
{
    public function __construct(
        private readonly Player                          $player,
        private readonly Camp                            $camp,
        private readonly ResearchTechDefinitionInterface $researchTechDefinition,
        private readonly int                             $level,
        private ResourcePack                             $cost
    )
    {
    }

    public function getCamp(): Camp
    {
        return $this->camp;
    }


    public function getCost(): ResourcePack
    {
        return $this->cost;
    }

    public function setCost(ResourcePack $cost): ResearchCostEvent
    {
        $this->cost = $cost;
        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getResearchTechDefinition(): ResearchTechDefinitionInterface
    {
        return $this->researchTechDefinition;
    }



}
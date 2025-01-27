<?php

namespace App\Model;

// TODO: Cleanup, make it POPO
use App\Entity\World\PlayerTech;

readonly class TechRequirement
{

    public function __construct(private array $techs)
    {}

    public function isSatisfied(PlayerTech $playerTech): bool
    {
        foreach ($this->techs as $tech => $level) {
            if (!$playerTech->hasLevel($tech, $level)) {
                return false;
            }
        }

        return true;
    }

    public function getRequiredTechs(): array
    {
        return $this->techs;
    }
}
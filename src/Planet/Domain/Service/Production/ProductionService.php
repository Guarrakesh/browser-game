<?php

namespace App\Planet\Domain\Service\Production;

use App\Planet\Domain\Entity\Planet;
use App\Shared\Model\ResourcePack;

class ProductionService
{


    public function getHourlyProduction(Planet $planet, float $universeSpeed, /* iterable $effects */): ResourcePack
    {
        $basicProduction = $planet->getBaseHourlyProduction();
        // TODO: dispatch event or other mechanism to allow "Effects" to change affect speed
        return $basicProduction->multiply($universeSpeed);
    }
}
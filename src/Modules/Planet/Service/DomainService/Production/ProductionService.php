<?php

namespace App\Modules\Planet\Service\DomainService\Production;

use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Shared\Model\ResourcePack;

class ProductionService
{


    public function getHourlyProduction(Planet $planet, float $universeSpeed, /* iterable $effects */): ResourcePack
    {
        $basicProduction = $planet->getBaseHourlyProduction();
        // TODO: dispatch event or other mechanism to allow "Effects" to change affect speed
        return $basicProduction->multiply($universeSpeed);
    }
}
<?php

namespace App\Modules\Planet\Dto;

use App\Modules\Shared\Dto\GameObjectLevel;
use App\Modules\Shared\Model\ResourcePack;

class EnergyDTO
{
    public function __construct(public float $yield, public float $consumption)
    {
    }

    public function getAvailable(): float
    {
        return $this->yield - $this->consumption;
    }
}
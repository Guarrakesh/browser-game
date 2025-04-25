<?php

namespace App\Planet\Dto;

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
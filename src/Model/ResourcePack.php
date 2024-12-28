<?php

namespace App\Model;

use App\Constants;

final class ResourcePack
{
    public function __construct(private float $concrete = 0, private float $metals = 0, private float $circuits = 0, private float $food = 0)
    {
    }

    public function addFromBuilding(string $building, float $amount): ResourcePack {
        if ($building === Constants::CONCRETE_EXTRACTOR) {
            $this->concrete += $amount;
        } elseif ($building === Constants::METAL_REFINERY) {
            $this->metals += $amount;
        } elseif ($building === Constants::CIRCUIT_ASSEMBLY_PLANT) {
            $this->circuits += $amount;
        } elseif ($building === Constants::HYDROPONIC_FARM) {
            $this->food += $amount;
        }

        return $this;
    }
    public function getConcrete(): float
    {
        return $this->concrete;
    }

    public function setConcrete(float $concrete): ResourcePack
    {
        $this->concrete = $concrete;
        return $this;
    }

    public function getMetals(): float
    {
        return $this->metals;
    }

    public function setMetals(float $metals): ResourcePack
    {
        $this->metals = $metals;
        return $this;
    }

    public function getCircuits(): float
    {
        return $this->circuits;
    }

    public function setCircuits(float $circuits): ResourcePack
    {
        $this->circuits = $circuits;
        return $this;
    }

    public function getFood(): float
    {
        return $this->food;
    }

    public function setFood(float $food): ResourcePack
    {
        $this->food = $food;
        return $this;
    }

    public function toSeconds(): ResourcePack
    {
        return new ResourcePack(
            $this->concrete / 3600,
            $this->metals / 3600,
            $this->circuits / 3600,
            $this->food / 3600
        );
    }

    public function multiply(float $multiplier): ResourcePack
    {
        $this->concrete *= $multiplier;
        $this->metals *= $multiplier;
        $this->circuits *= $multiplier;
        $this->food *= $multiplier;

        return $this;
    }

}
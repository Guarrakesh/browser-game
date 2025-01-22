<?php

namespace App\Object;

use App\Constants;

/**
 * A DTO class for resources. It has utility methods to do calculations on all the resources
 */
final class ResourcePack
{
    public function __construct(private float $concrete = 0, private float $metals = 0, private float $circuits = 0, private float $food = 0)
    {
    }

    /**
     * Adds a single resource type based on the given building
     * @param string $building
     * @param float $amount
     * @return $this
     */
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


    /**
     * @return ResourcePack A NEW ResourcePack instance where the resources are given in seconds
     */
    public function toSeconds(): ResourcePack
    {
        return new ResourcePack(
            $this->concrete / 3600,
            $this->metals / 3600,
            $this->circuits / 3600,
            $this->food / 3600
        );
    }

    /**
     * Adds resources from a given pack. If the pack has negative values, resources are subtracted
     * @param ResourcePack $pack
     * @return $this A NEW ResourcePack instance with added resources
     */
    public function add(ResourcePack $pack): ResourcePack
    {
        return new ResourcePack(
            $this->concrete + $pack->concrete,
            $this->metals + $pack->metals,
            $this->circuits + $pack->circuits,
            $this->food + $pack->food
        );
    }

    /**
     * @param float $multiplier
     * @return $this A NEW ResourcePack instance with multiplied resources.
     */
    public function multiply(float $multiplier): ResourcePack
    {
        return new ResourcePack(
            $this->concrete * $multiplier,
            $this->metals * $multiplier,
            $this->circuits * $multiplier,
            $this->food * $multiplier
        );

    }

    /**
     * Assign to each resource the result of the given callback.
     * The first argument passed is the current resource, The second argument is the resource name.
     * @return ResourcePack A NEW ResourcePack instanced with updated values
     */
    public function map(callable $callback): ResourcePack
    {
        $concrete = $callback($this->concrete, Constants::CONCRETE);
        $metals = $callback($this->metals, Constants::METALS);
        $circuits = $callback($this->circuits, Constants::CIRCUITS);
        $food = $callback($this->food, Constants::FOOD);

        return new ResourcePack($concrete, $metals, $circuits, $food);
    }

    public function reduce(callable $callback, float $initialValue = 0): float
    {
        $acc = $callback($initialValue, $this->concrete, Constants::CONCRETE);
        $acc = $callback($acc, $this->metals, Constants::METALS);
        $acc = $callback($acc, $this->circuits, Constants::CIRCUITS);
        return $callback($acc, $this->food, Constants::FOOD);
    }

    public function total(): float
    {
        return $this->concrete + $this->metals + $this->circuits + $this->food;
    }


}
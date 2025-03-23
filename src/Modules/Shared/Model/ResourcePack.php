<?php

namespace App\Modules\Shared\Model;

use App\Modules\Shared\Constants;

/**
 * An Immutable ValueObject that stores resources amount.
 */
final class ResourcePack
{
    public function __construct(private float $concrete = 0, private float $metals = 0, private float $polymers = 0, private float $food = 0)
    {
    }

    /**
     * Adds a single resource type based on the given building
     * @param string $building
     * @param float $amount
     * @return $this
     */
    public function addFromBuilding(string $building, float $amount): ResourcePack
    {
        $new = new ResourcePack($this->concrete, $this->metals, $this->polymers, $this->food);
        if ($building === Constants::CONCRETE_EXTRACTOR) {
            $new->concrete = $this->concrete + $amount;
        } elseif ($building === Constants::METAL_REFINERY) {
            $new->metals = $this->metals + $amount;
        } elseif ($building === Constants::CIRCUIT_ASSEMBLY_PLANT) {
            $new->polymers = $this->polymers + $amount;
        } elseif ($building === Constants::HYDROPONIC_FARM) {
            $new->food =  $this->food + $amount;
        }

        return $new;
    }
    public function getConcrete(): float
    {
        return $this->concrete;
    }

    public function getMetals(): float
    {
        return $this->metals;
    }

    public function getPolymers(): float
    {
        return $this->polymers;
    }


    public function getFood(): float
    {
        return $this->food;
    }


    /**
     * @return ResourcePack a new ResourcePack instance that represents the resources as production per second.
     */
    public function toSeconds(): ResourcePack
    {
        return new ResourcePack(
            $this->concrete / 3600,
            $this->metals / 3600,
            $this->polymers / 3600,
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
            $this->polymers + $pack->polymers,
            $this->food + $pack->food
        );
    }

    /**
     * @param float $multiplier
     * @return $this A NEW ResourcePack instance with multiplied resources.
     */
    public function multiply(float $multiplier, bool $round = false): ResourcePack
    {
        return new ResourcePack(
            round($this->concrete * $multiplier, $round ? 0 :1),
            round($this->metals * $multiplier, $round ? 0: 1),
            round($this->polymers * $multiplier, $round ? 0: 1),
            round($this->food * $multiplier, $round ? 0: 1),
        );

    }

    public function diff(ResourcePack $pack): ResourcePack
    {
        return $this->add($pack->multiply(-1));
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
        $polymers = $callback($this->polymers, Constants::POLYMERS);
        $food = $callback($this->food, Constants::FOOD);

        return new ResourcePack($concrete, $metals, $polymers, $food);
    }

    public function reduce(callable $callback, float $initialValue = 0): float
    {
        $acc = $callback($initialValue, $this->concrete, Constants::CONCRETE);
        $acc = $callback($acc, $this->metals, Constants::METALS);
        $acc = $callback($acc, $this->polymers, Constants::POLYMERS);

        return $callback($acc, $this->food, Constants::FOOD);
    }

    public function total(): float
    {
        return $this->concrete + $this->metals + $this->polymers + $this->food;
    }

    public function toArray(): array
    {
        return [
            $this->concrete,
            $this->metals,
            $this->polymers,
            $this->food
        ];
    }

    public static function fromArray(array $data): ResourcePack
    {
        return new ResourcePack($data[0] ?? 0, $data[1] ?? 0, $data[2] ?? 0, $data[3] ?? 0);
    }

    public static function fromIdentity(float $value): ResourcePack
    {
        return new ResourcePack($value, $value, $value, $value);
    }

}
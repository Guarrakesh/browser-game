<?php

namespace App\Model\Building;

use ArrayAccess;
use Iterator;

class CampBuildingList implements Iterator, ArrayAccess
{

    /**
     * @param array<string, int> $buildings
     */
    public function __construct(private array $buildings = []) {}

    public function getBuildings(): array
    {
        return $this->buildings;
    }

    public function hasBuilding(string $name): bool
    {
        return array_key_exists($name, $this->buildings);
    }

    public function addBuilding(string $name, int $level): self
    {
        $this->buildings[$name] = $level;

        return $this;
    }

    public function removeBuilding(string $name): self
    {
        if ($this->hasBuilding($name)) {
            unset($this->buildings[$name]);
        }

        return $this;
    }

    public function getBuildingLevel(string $name): ?int
    {
        return $this->buildings[$name] ?? 0;
    }

    public function current(): mixed
    {
        return current($this->buildings);
    }

    public function next(): void
    {
        next($this->buildings);
    }

    public function key(): mixed
    {
        return key($this->buildings);
    }

    public function valid(): bool
    {
        return key($this->buildings) !== null;
    }

    public function rewind(): void
    {
        reset($this->buildings);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->buildings[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->buildings[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!is_int($value) || !is_string($offset)) {
            throw new \InvalidArgumentException("Invalid offset or value set.");
        }
        $this->addBuilding($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException("Invalid offset type. String expected.");
        }

        $this->removeBuilding($offset);
    }
}
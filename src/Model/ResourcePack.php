<?php

namespace App\Model;

final readonly class ResourcePack
{
    public function __construct(private int $concrete, private int $metals, private int $circuits, private int $food)
    {
    }

    public function getConcrete(): int
    {
        return $this->concrete;
    }

    public function getMetals(): int
    {
        return $this->metals;
    }

    public function getCircuits(): int
    {
        return $this->circuits;
    }

    public function getFood(): int
    {
        return $this->food;
    }




}
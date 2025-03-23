<?php

namespace App\Modules\Planet\Dto;

class DroneAllocationDTO
{
    public string $pool;

    public int $quantity;

    public function __construct(string $pool, int $quantity)
    {
        $this->pool = $pool;
        $this->quantity = $quantity;
    }


}
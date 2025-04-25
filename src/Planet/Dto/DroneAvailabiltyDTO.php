<?php

namespace App\Planet\Dto;

class DroneAvailabiltyDTO
{
    public int $totalDrones;
    public int $usedDrones;

    /** @var DroneAllocationDTO[] */
    public array $allocations;

    public function __construct(int $totalDrones, int $usedDrones, array $allocations)
    {
        $this->totalDrones = $totalDrones;
        $this->usedDrones = $usedDrones;
        $this->allocations = $allocations;
    }


    public function getAvailableDrones(): int
    {
        return $this->totalDrones - $this->usedDrones;
    }
}
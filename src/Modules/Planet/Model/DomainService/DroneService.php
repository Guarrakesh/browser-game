<?php

namespace App\Modules\Planet\Model\DomainService;

use App\Modules\Planet\Infra\Repository\DroneAllocationRepository;

class DroneService
{
    public function __construct(
        DroneAllocationRepository $droneAllocationRepository
    )
    {
    }



}
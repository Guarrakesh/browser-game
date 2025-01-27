<?php

namespace App\Modules\Construction\DTO;

use App\Entity\World\Queue\Queue;
use AutoMapper\Attribute\MapFrom;
use AutoMapper\Attribute\Mapper;

#[Mapper(source: ['array', Queue::class])]
class ConstructionQueueDTO
{
    /**
     * @var array<ConstructionQueueJobDTO>
     */
    #[MapFrom(source: Queue::class, property: 'jobs')]
    public array $jobs = [];

    public int $planetId;
}
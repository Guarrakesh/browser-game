<?php

namespace App\Modules\Construction\DTO;

use App\Entity\World\Queue\Queue;
use App\Modules\Core\Service\AutoMapperTransformer\QueueTransformer;
use AutoMapper\Attribute\MapFrom;
use AutoMapper\Attribute\Mapper;

#[Mapper(source: ['array', Queue::class])]
class ConstructionQueueDTO
{
    #[MapFrom(source: Queue::class, transformer: QueueTransformer::class )]
    public array $jobs = [];

    public int $planetId;
}
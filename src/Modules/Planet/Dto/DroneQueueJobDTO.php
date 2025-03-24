<?php

namespace App\Modules\Planet\Dto;

use App\Modules\Core\DTO\QueueJobDTO;
use DateTimeImmutable;
use Symfony\Component\Clock\Clock;

class DroneQueueJobDTO extends QueueJobDTO
{
    public function __construct(
        public int $id,
        int $duration,
        DateTimeImmutable $startedAt,
        DateTimeImmutable $completedAt,
        ?DateTimeImmutable $cancelledAt = null,

    ) {
        parent::__construct($id, $duration, $completedAt, $startedAt, $cancelledAt);
    }


}
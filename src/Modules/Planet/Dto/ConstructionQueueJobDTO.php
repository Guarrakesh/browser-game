<?php

namespace App\Modules\Planet\Dto;

use App\Modules\Core\DTO\QueueJobDTO;
use DateTimeImmutable;

class ConstructionQueueJobDTO extends QueueJobDTO
{
    public function __construct(
        public int $id,
        public readonly string $buildingName,
        public readonly int $level,
        public readonly int $duration,
        public DateTimeImmutable $startedAt,
        public DateTimeImmutable $completedAt
    ) {}

    public function remainingTime(): \DateInterval
    {
        return $this->completedAt->diff(new DateTimeImmutable());
    }


}
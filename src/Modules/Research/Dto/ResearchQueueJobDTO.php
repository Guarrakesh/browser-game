<?php

namespace App\Modules\Research\Dto;

use App\Modules\Core\DTO\QueueJobDTO;
use DateTimeImmutable;

class ResearchQueueJobDTO extends QueueJobDTO
{
    public function __construct(
        public int               $id,
        public readonly string   $techName,
        public readonly int      $duration,
        public DateTimeImmutable $startedAt,
        public DateTimeImmutable $completedAt
    ) {}

    public function remainingTime(): \DateInterval
    {
        return $this->completedAt->diff(new DateTimeImmutable());
    }


}
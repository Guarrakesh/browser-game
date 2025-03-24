<?php

namespace App\Modules\Research\Dto;

use App\Modules\Core\DTO\QueueJobDTO;
use DateTimeImmutable;

class ResearchQueueJobDTO extends QueueJobDTO
{
    public function __construct(
        public int             $id,
        public readonly string $techName,
        public int             $duration,
        DateTimeImmutable      $startedAt,
        DateTimeImmutable      $completedAt,
        ?DateTimeImmutable     $cancelledAt = null
    )
    {
        parent::__construct(
            $id,
            $duration,
            $completedAt,
            $startedAt,
            $cancelledAt
        );
    }


    public
    function remainingTime(): \DateInterval
    {
        return $this->completedAt->diff(new DateTimeImmutable());
    }


}
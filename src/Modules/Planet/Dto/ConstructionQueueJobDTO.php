<?php

namespace App\Modules\Planet\Dto;

use App\Modules\Core\DTO\QueueJobDTO;
use DateTimeImmutable;

class ConstructionQueueJobDTO extends QueueJobDTO
{
    public function __construct(
        int                    $id,
        public readonly string $buildingName,
        public readonly int    $level,
        int                    $duration,
        DateTimeImmutable      $startedAt,
        DateTimeImmutable      $completedAt,
        ?DateTimeImmutable     $cancelledAt = null,
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


}
<?php

namespace App\Modules\Core\DTO;

use DateInterval;
use DateTimeImmutable;

class QueueJobDTO
{
    public int $id;

    public DateTimeImmutable $completedAt;
    public DateTimeImmutable $startedAt;
    public ?DateTimeImmutable $cancelledAt = null;

    public ?DateInterval $remainingTime;
}
<?php

namespace App\Modules\Construction\DTO;

use DateTimeImmutable;

class ConstructionQueueJobDTO
{
    public int $id;

    public string $buildingName;
    public DateTimeImmutable $completedAt;
    public DateTimeImmutable $startedAt;
    public ?DateTimeImmutable $cancelledAt = null;

    public int $level;

}
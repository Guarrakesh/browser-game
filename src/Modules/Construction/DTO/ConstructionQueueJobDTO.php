<?php

namespace App\Modules\Construction\DTO;

use App\Modules\Core\DTO\QueueJobDTO;
use DateTimeImmutable;

class ConstructionQueueJobDTO extends QueueJobDTO
{

    public string $buildingName;

    public int $level;



}
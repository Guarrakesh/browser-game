<?php

namespace App\Modules\Planet\Model\Entity\Drone;

use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Shared\Model\Entity\QueueJob;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DroneQueueJob extends QueueJob
{
    #[ORM\Column]
    private int $planetId;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: false)]
    private ?array $resourcesUsed = null;


    public function __construct(int $planetId, ?array $resourcesUsed)
    {
        $this->planetId = $planetId;
        $this->resourcesUsed = $resourcesUsed;
    }

    public function getPlanetId(): int
    {
        return $this->planetId;
    }

    public function getNumDrones(): int
    {
        return $this->energy_base_yields;
    }

    public function getResourcesUsed(): ?array
    {
        return $this->resourcesUsed;
    }


}
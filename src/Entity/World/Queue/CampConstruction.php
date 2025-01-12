<?php

namespace App\Entity\World\Queue;

use App\Entity\World\Camp;
use App\Model\ResourcePack;
use App\Repository\CampConstructionRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampConstructionRepository::class)]
class CampConstruction extends QueueJob
{

    #[ORM\ManyToOne(inversedBy: 'campConstructions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Camp $camp = null;

    #[ORM\Column(length: 255)]
    private ?string $buildingName = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $level = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: false)]
    private ?array $resourcesUsed = null;


    public function getCamp(): ?Camp
    {
        return $this->camp;
    }

    public function setCamp(?Camp $camp): static
    {
        $this->camp = $camp;

        return $this;
    }

    public function getBuildingName(): ?string
    {
        return $this->buildingName;
    }

    public function setBuildingName(string $buildingName): static
    {
        $this->buildingName = $buildingName;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getRemainingTime(): DateInterval
    {
        $now = new DateTimeImmutable();

        return $now->diff($this->getCompletedAt());

    }

    public function getResourcesUsed(): ResourcePack
    {
        $resources = $this->resourcesUsed;

        return new ResourcePack($resources[0] ?? 0, $resources[1] ?? 0, $resources[2] ?? 0, $resources[3] ?? 0);
    }

    public function setResourcesUsed(ResourcePack $resourcesUsed): CampConstruction
    {
        $this->resourcesUsed = [
            $resourcesUsed->getConcrete(),
            $resourcesUsed->getMetals(),
            $resourcesUsed->getCircuits(),
            $resourcesUsed->getFood()
        ];

        return $this;
    }




}

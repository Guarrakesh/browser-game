<?php

namespace App\Entity\World\Queue;

use App\Entity\World\Camp;
use App\Repository\CampConstructionRepository;
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

}

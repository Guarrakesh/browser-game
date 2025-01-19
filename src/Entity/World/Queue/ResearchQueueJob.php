<?php

namespace App\Entity\World\Queue;

use App\Entity\World\Camp;
use App\Entity\World\Player;
use App\Repository\ResearchQueueJobRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResearchQueueJobRepository::class)]
class ResearchQueueJob
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $researchName = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $level = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $resourcesUsed = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $player = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Camp $camp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResearchName(): ?string
    {
        return $this->researchName;
    }

    public function setResearchName(string $researchName): static
    {
        $this->researchName = $researchName;

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

    public function getResourcesUsed(): array
    {
        return $this->resourcesUsed;
    }

    public function setResourcesUsed(array $resourcesUsed): static
    {
        $this->resourcesUsed = $resourcesUsed;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): static
    {
        $this->player = $player;

        return $this;
    }


    public function getCamp(): ?Camp
    {
        return $this->camp;
    }

    public function setCamp(?Camp $camp): static
    {
        $this->camp = $camp;

        return $this;
    }
}

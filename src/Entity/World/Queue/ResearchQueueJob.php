<?php

namespace App\Entity\World\Queue;

use App\CurveCalculator\CalculatorConfig;
use App\Entity\World\Camp;
use App\Entity\World\Player;
use App\Object\ResourcePack;
use App\Repository\ResearchQueueJobRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LogicException;

#[ORM\Entity(repositoryClass: ResearchQueueJobRepository::class)]
class ResearchQueueJob
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $techName = null;

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

    public function getTechName(): ?string
    {
        return $this->techName;
    }

    public function setTechName(string $techName): static
    {
        $this->techName = $techName;

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

    public function getResourcesUsed(): ResourcePack
    {
        $resources = $this->resourcesUsed;

        return new ResourcePack($resources[0] ?? 0, $resources[1] ?? 0, $resources[2] ?? 0, $resources[3] ?? 0);
    }

    public function setResourcesUsed(ResourcePack $resourcesUsed): ResearchQueueJob
    {
        $this->resourcesUsed = [
            $resourcesUsed->getConcrete(),
            $resourcesUsed->getMetals(),
            $resourcesUsed->getCircuits(),
            $resourcesUsed->getFood()
        ];

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

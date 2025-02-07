<?php

namespace App\Modules\Planet\Model;

use App\Entity\World\Player;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Planet\Model\Entity\QueueJob;
use App\Modules\Shared\Model\ResourcePack;
use App\Repository\ResearchQueueJobRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResearchQueueJobRepository::class)]
class ResearchQueueJob extends QueueJob
{

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
    private ?Planet $planet = null;


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


    public function getPlanet(): ?Planet
    {
        return $this->planet;
    }

    public function setPlanet(?Planet $planet): static
    {
        $this->planet = $planet;

        return $this;
    }

}

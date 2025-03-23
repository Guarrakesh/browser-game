<?php

namespace App\Modules\Research\Model\Entity;

use App\Entity\World\Player;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Research\Dto\ObjectDefinition\ResearchTechDefinitionInterface;
use App\Modules\Research\Model\TechDefinitionAwareInterface;
use App\Modules\Shared\Model\Entity\QueueJob;
use App\Modules\Shared\Model\ResourcePack;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ResearchQueueJob extends QueueJob implements TechDefinitionAwareInterface
{

    #[ORM\Column(length: 255)]
    private ?string $techName = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $resourcesUsed = [];

    #[ORM\Column(nullable: false)]
    private int $playerId;

    #[ORM\Column(nullable: false)]
    private int $planetId;

    private ?ResearchTechDefinitionInterface $definition = null;

    public function __construct(int $playerId, int $planetId, string $techName, int $duration, ResourcePack $cost)
    {
        $this->planetId = $planetId;
        $this->playerId = $playerId;
        $this->techName = $techName;
        $this->duration = $duration;
        $this->resourcesUsed = $cost->toArray();
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
            $resourcesUsed->getPolymers(),
            $resourcesUsed->getFood()
        ];

        return $this;
    }

    public function getPlayerId(): int
    {
        return $this->playerId;
    }

    public function getPlanetId(): int
    {
        return $this->planetId;
    }


    public function getDefinition(): ?ResearchTechDefinitionInterface
    {
        return $this->definition;
    }

    public function setDefinition(?ResearchTechDefinitionInterface $definition): TechDefinitionAwareInterface
    {
        $this->definition = $definition;

        return $this;
    }
}

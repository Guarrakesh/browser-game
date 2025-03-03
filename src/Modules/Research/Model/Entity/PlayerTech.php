<?php

namespace App\Modules\Research\Model\Entity;

use App\Entity\World\Player;
use App\Modules\Research\Dto\ObjectDefinition\ResearchTechDefinitionInterface;
use App\Modules\Research\Infra\Repository\PlayerTechRepository;
use App\Modules\Research\Model\TechDefinitionAwareInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;

#[ORM\Entity(repositoryClass: PlayerTechRepository::class)]
#[ORM\UniqueConstraint(fields: ['playerId', 'techName'])]
class PlayerTech implements TechDefinitionAwareInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private string $techName;

    #[ORM\Column]
    private ?int $playerId = null;

    private ?ResearchTechDefinitionInterface $definition = null;

    #[ORM\Column(nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct(int $playerId, string $techName, ResearchTechDefinitionInterface $definition)
    {
        $this->playerId = $playerId;
        $this->techName = $techName;
        $this->definition = $definition;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTechName(): ?string
    {
        return $this->techName;
    }

    public function getDefinition(): ?ResearchTechDefinitionInterface
    {
        return $this->definition;
    }

    public function setDefinition(?ResearchTechDefinitionInterface $definition): TechDefinitionAwareInterface
    {
        $this->definition = $definition;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getPlayerId(): ?int
    {
        return $this->playerId;
    }


}

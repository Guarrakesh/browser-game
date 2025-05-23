<?php

namespace App\Planet\Domain\Entity;

use App\Planet\GameObject\Building\BuildingDefinition;
use App\Planet\GameObject\Building\BuildingDefinitionInterface;
use App\Shared\Dto\GameObject;
use App\Shared\Dto\GameObjectLevel;
use App\Shared\Dto\GameObjectWithRequirements;
use App\Shared\Model\ObjectType;
use AutoMapper\Attribute\Mapper;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\UniqueConstraint(name: 'planet_building_unique', columns: ['planet_id', 'name'])]
#[ORM\Entity]
#[Mapper(target: GameObjectWithRequirements::class)]
class PlanetBuilding implements BuildingDefinitionAwareInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $level = null;

    #[ORM\Column(nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Timestampable]
    private ?\DateTimeImmutable $updatedAt = null;


    #[ORM\ManyToOne(inversedBy: 'planetBuildings')]
    #[ORM\JoinColumn(name: 'planet_id', nullable: false)]
    #[Ignore]
    private ?Planet $planet = null;


    private ?BuildingDefinitionInterface $definition = null;

    public function __construct(?Planet $planet, ?BuildingDefinition $definition, ?int $level)
    {
        $this->level = $level;
        $this->name = $definition->getName();
        $this->planet = $planet;
        $this->definition = $definition;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[Ignore]
    public function getPlanet(): ?Planet
    {
        return $this->planet;
    }

    public function setPlanet(?Planet $planet): static
    {
        $this->planet = $planet;

        return $this;
    }

    public function getDefinition(): ?BuildingDefinitionInterface
    {
        return $this->definition;
    }

    public function setDefinition(?BuildingDefinitionInterface $definition): PlanetBuilding
    {
        $this->definition = $definition;
        return $this;
    }


    public function getAsGameObject(): GameObjectLevel
    {
        return new GameObjectLevel(
            new GameObject($this->name, ObjectType::Building),
            $this->level,
            $this->definition,
        );
    }

    public function getBuildingName(): ?string
    {
        return $this->name;
    }
}

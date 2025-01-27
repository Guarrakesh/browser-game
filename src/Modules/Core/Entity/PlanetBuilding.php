<?php

namespace App\Modules\Core\Entity;

use App\Modules\Core\DTO\PlanetBuildingDTO;
use App\Modules\Core\Repository\PlanetBuildingRepository;
use App\ObjectDefinition\Building\BuildingDefinition;
use AutoMapper\Attribute\Mapper;
use AutoMapper\Attribute\MapTo;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: PlanetBuildingRepository::class)]
#[ORM\UniqueConstraint(name: 'planet_building_unique', columns: ['planet_id', 'name'])]
#[Mapper(target: PlanetBuildingDTO::class)]
class PlanetBuilding
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

    #[MapTo()]
    private ?BuildingDefinition $definition = null;

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

    public function getDefinition(): ?BuildingDefinition
    {
        return $this->definition;
    }

    public function setDefinition(?BuildingDefinition $definition): PlanetBuilding
    {
        $this->definition = $definition;
        return $this;
    }



}

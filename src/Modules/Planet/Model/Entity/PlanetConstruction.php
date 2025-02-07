<?php

namespace App\Modules\Planet\Model\Entity;

use App\Modules\Planet\Dto\ObjectDefinition\Building\BuildingDefinition;
use App\Modules\Shared\Dto\GameObject;
use App\Modules\Shared\Model\ObjectType;
use App\Modules\Shared\Model\ResourcePack;
use AutoMapper\Attribute\Mapper;
use AutoMapper\Attribute\MapTo;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[Mapper(target: 'array')]
#[ORM\Entity]
class PlanetConstruction extends QueueJob implements BuildingDefinitionAwareInterface
{

    #[ORM\JoinColumn(name: 'planet_id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Planet::class, inversedBy: 'constructions')]
    private ?Planet $planet = null;

    #[ORM\Column(length: 255)]
    #[MapTo(property: 'buildingName')]
    private ?string $buildingName = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isDowngrade = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $processed = false;
    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: false)]
    private ?array $resourcesUsed = null;

    /** @var BuildingDefinition|null Does not come from ORM but from a Listener. */
    private ?BuildingDefinition $buildingDefinition = null;

    #[ORM\Column()]
    private ?int $level = null;

    public function __construct(Planet $planet, BuildingDefinition $buildingDefinition, int $level, ResourcePack $resourcesUsed, bool $isDowngrade)
    {
        $this->planet = $planet;
        $this->buildingName = $buildingDefinition->getName();
        $this->isDowngrade = $isDowngrade;
        $this->buildingDefinition = $buildingDefinition;
        $this->resourcesUsed = $resourcesUsed->toArray();
        $this->level = $level;
        $this->startedAt = new \DateTimeImmutable();
    }



    public function getBuildingName(): ?string
    {
        return $this->buildingName;
    }



    public function getResourcesUsed(): ResourcePack
    {
        return ResourcePack::fromArray($this->resourcesUsed);
    }


    public function gameObject(): GameObject
    {
        return new GameObject($this->buildingName, ObjectType::Building);
    }

    public function getDefinition(): ?BuildingDefinition
    {
        return $this->buildingDefinition;
    }

    public function isProcessed(): bool
    {
        return $this->processed;
    }

    public function markAsProcessed(): static
    {
        $this->processed = true;

        return $this;
    }


    public function setDefinition(?BuildingDefinition $definition): BuildingDefinitionAwareInterface
    {
        $this->buildingDefinition = $definition;

        return $this;
    }

    public function isDowngrade(): bool
    {
        return $this->isDowngrade;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): PlanetConstruction
    {
        $this->level = $level;

        return $this;
    }





}

<?php

namespace App\Entity\World\Queue;

use App\Modules\Core\Entity\Planet;
use App\Object\ResourcePack;
use App\Repository\PlanetConstructionRepository;
use AutoMapper\Attribute\Mapper;
use AutoMapper\Attribute\MapTo;
use DateInterval;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[Mapper(target: 'array')]
#[ORM\Entity(repositoryClass: PlanetConstructionRepository::class)]
class PlanetConstruction extends QueueJob
{

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'planet_id', nullable: false)]
    private ?Planet $planet = null;

    #[ORM\Column(length: 255)]
    #[MapTo(property: 'buildingName')]
    private ?string $buildingName = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $level = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: false)]
    private ?array $resourcesUsed = null;


    public function getPlanet(): ?Planet
    {
        return $this->planet;
    }

    public function setPlanet(?Planet $planet): static
    {
        $this->planet = $planet;

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

    public function getResourcesUsed(): ResourcePack
    {
        $resources = $this->resourcesUsed;

        return new ResourcePack($resources[0] ?? 0, $resources[1] ?? 0, $resources[2] ?? 0, $resources[3] ?? 0);
    }

    public function setResourcesUsed(ResourcePack $resourcesUsed): PlanetConstruction
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

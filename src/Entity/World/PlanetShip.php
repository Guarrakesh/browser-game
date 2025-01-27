<?php

namespace App\Entity\World;

use App\Modules\Core\Entity\Planet;
use App\Repository\ShipRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShipRepository::class)]
#[ORM\Table('planet_ship')]
class PlanetShip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ships')]
    private ?Fleet $fleet = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column]
    private array $components = [];

    #[ORM\Column]
    private ?int $mass = null;

    #[ORM\Column]
    private ?float $speed = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?float $hull = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Planet $planet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFleet(): ?Fleet
    {
        return $this->fleet;
    }

    public function setFleet(?Fleet $fleet): static
    {
        $this->fleet = $fleet;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getComponents(): array
    {
        return $this->components;
    }

    public function setComponents(array $components): static
    {
        $this->components = $components;

        return $this;
    }

    public function getMass(): ?int
    {
        return $this->mass;
    }

    public function setMass(int $mass): static
    {
        $this->mass = $mass;

        return $this;
    }

    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    public function setSpeed(float $speed): static
    {
        $this->speed = $speed;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getHull(): ?float
    {
        return $this->hull;
    }

    public function setHull(float $hull): static
    {
        $this->hull = $hull;

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

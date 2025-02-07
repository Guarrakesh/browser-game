<?php

namespace App\Modules\Planet\Model\Entity;

use App\Repository\FleetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FleetRepository::class)]
class Fleet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $position = [];

    /**
     * @var Collection<int, PlanetShip>
     */
    #[ORM\OneToMany(targetEntity: PlanetShip::class, mappedBy: 'fleet')]
    private Collection $ships;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Planet $planet = null;

    public function __construct()
    {
        $this->ships = new ArrayCollection();
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

    public function getPosition(): array
    {
        return $this->position;
    }

    public function setPosition(array $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection<int, PlanetShip>
     */
    public function getShips(): Collection
    {
        return $this->ships;
    }

    public function addShip(PlanetShip $ship): static
    {
        if (!$this->ships->contains($ship)) {
            $this->ships->add($ship);
            $ship->setFleet($this);
        }

        return $this;
    }

    public function removeShip(PlanetShip $ship): static
    {
        if ($this->ships->removeElement($ship)) {
            // set the owning side to null (unless already changed)
            if ($ship->getFleet() === $this) {
                $ship->setFleet(null);
            }
        }

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

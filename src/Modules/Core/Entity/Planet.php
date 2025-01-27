<?php

namespace App\Modules\Core\Entity;

use App\Entity\World\Player;
use App\Entity\World\Storage;
use App\Modules\Core\DTO\PlanetDTO;
use App\Modules\Core\Repository\PlanetRepository;
use App\Object\ResourcePack;
use AutoMapper\Attribute\Mapper;
use AutoMapper\Attribute\MapTo;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Serializer\Attribute\Ignore;

#[Mapper(target: [PlanetDTO::class, 'array'])]
#[ORM\Entity(repositoryClass: PlanetRepository::class)]
class Planet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $coordX = null;

    #[ORM\Column]
    private ?int $coordY = null;

    #[ORM\Column]
    private ?int $points = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\OneToOne(mappedBy: 'planet', cascade: ['persist', 'remove'])]
    #[MapTo(transformer: 'source.getStorageAsPack()')]
    private Storage $storage;


    /**
     * @var Collection<int, PlanetBuilding>
     */
    #[ORM\OneToMany(targetEntity: PlanetBuilding::class, mappedBy: 'planet', orphanRemoval: true, indexBy: 'name')]
    #[MapTo(target: PlanetDTO::class, property: 'buildings')]
    /** @var Collection<string, PlanetBuilding> $planetBuildings */
    private Collection $planetBuildings;

    #[ORM\ManyToOne(inversedBy: 'planets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $player = null;


    #[ORM\Column(nullable: true)]
    #[Timestampable]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->planetBuildings = new ArrayCollection();
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

    public function getCoordX(): ?int
    {
        return $this->coordX;
    }

    public function setCoordX(int $coordX): static
    {
        $this->coordX = $coordX;

        return $this;
    }

    public function getCoordY(): ?int
    {
        return $this->coordY;
    }

    public function setCoordY(int $coordY): static
    {
        $this->coordY = $coordY;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getStorage(): Storage
    {
        return $this->storage;
    }

    public function setStorage(Storage $storage): static
    {
        // set the owning side of the relation if necessary
        if ($storage->getPlanet() !== $this) {
            $storage->setPlanet($this);
        }

        $this->storage = $storage;

        return $this;
    }

    /**
     * @return Collection<string, PlanetBuilding>
     */
    public function getPlanetBuildings(): Collection
    {
        return $this->planetBuildings;
    }

    public function addPlanetBuilding(PlanetBuilding $planetBuilding): static
    {
        if (!$this->planetBuildings->contains($planetBuilding)) {
            $this->planetBuildings->set($planetBuilding->getName(), $planetBuilding);
            $planetBuilding->setPlanet($this);
        }

        return $this;
    }

    public function removePlanetBuilding(PlanetBuilding $planetBuilding): static
    {
        if ($this->planetBuildings->removeElement($planetBuilding)) {
            // set the owning side to null (unless already changed)
            if ($planetBuilding->getPlanet() === $this) {
                $planetBuilding->setPlanet(null);
            }
        }

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

    #[Ignore]
    public function getBuilding(string $name): ?PlanetBuilding
    {
        return $this->planetBuildings[$name] ?? null;
    }


    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): Planet
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): Planet
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getStorageAsPack(): ResourcePack
    {
        return $this->storage->getAsPack();
    }

    public function getMaxStorage(): int
    {
        return $this->storage->getMaxStorage();
    }
}

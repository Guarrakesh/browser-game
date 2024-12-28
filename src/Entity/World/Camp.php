<?php

namespace App\Entity\World;

use App\Constants;
use App\Repository\CampRepository;
use App\Service\Camp\Building\BuildingConfigProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampRepository::class)]
class Camp
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

    #[ORM\OneToOne(mappedBy: 'camp', cascade: ['persist', 'remove'])]
    private ?Storage $storage = null;

    /**
     * @var Collection<int, CampBuilding>
     */
    #[ORM\OneToMany(targetEntity: CampBuilding::class, mappedBy: 'camp', orphanRemoval: true, indexBy: 'type')]
    private Collection $campBuildings;

    #[ORM\ManyToOne(inversedBy: 'camps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $player = null;

    public function __construct()
    {
        $this->campBuildings = new ArrayCollection();
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

    public function getStorage(): ?Storage
    {
        return $this->storage;
    }

    public function setStorage(Storage $storage): static
    {
        // set the owning side of the relation if necessary
        if ($storage->getCamp() !== $this) {
            $storage->setCamp($this);
        }

        $this->storage = $storage;

        return $this;
    }

    /**
     * @return Collection<int, CampBuilding>
     */
    public function getCampBuildings(): Collection
    {
        return $this->campBuildings;
    }

    public function addCampBuilding(CampBuilding $campBuilding): static
    {
        if (!$this->campBuildings->contains($campBuilding)) {
            $this->campBuildings->add($campBuilding);
            $campBuilding->setCamp($this);
        }

        return $this;
    }

    public function removeCampBuilding(CampBuilding $campBuilding): static
    {
        if ($this->campBuildings->removeElement($campBuilding)) {
            // set the owning side to null (unless already changed)
            if ($campBuilding->getCamp() === $this) {
                $campBuilding->setCamp(null);
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

    public function getBuilding(string $type): ?CampBuilding
    {
        return $this->campBuildings[$type] ?? null;
    }

    public function getMaxStorage(BuildingConfigProvider $storageConfig): int
    {
        $bay = $this->getBuilding(Constants::STORAGE_BAY);
        if (!$bay) {
            $maxStorage = 0;
        } else {
            $maxStorage =
                $storageConfig->getConfig('max_storage')
                * ($storageConfig->getIncreaseFactor() ** max($bay->getLevel() - 1, 0));
        }

        return $maxStorage;
    }


}

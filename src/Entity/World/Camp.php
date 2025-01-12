<?php

namespace App\Entity\World;

use App\Entity\World\Queue\CampConstruction;
use App\Repository\CampRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;

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
    private Storage $storage;

    /**
     * @var Collection<int, CampBuilding>
     */
    #[ORM\OneToMany(targetEntity: CampBuilding::class, mappedBy: 'camp', orphanRemoval: true, indexBy: 'name')]
    private Collection $campBuildings;

    #[ORM\ManyToOne(inversedBy: 'camps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $player = null;

    /**
     * @var Collection<int, CampConstruction>
     */
    #[ORM\OneToMany(targetEntity: CampConstruction::class, mappedBy: 'camp', cascade: ['persist', 'refresh', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['startedAt' => 'ASC', 'level' => 'ASC'])]
    private Collection $campConstructions;

    #[ORM\Column(nullable: true)]
    #[Timestampable]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->campBuildings = new ArrayCollection();
        $this->campConstructions = new ArrayCollection();
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
            $this->campBuildings->set($campBuilding->getName(), $campBuilding);
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

    public function getBuilding(string $name): ?CampBuilding
    {
        return $this->campBuildings[$name] ?? null;
    }

    /**
     * @return Collection<int, CampConstruction>
     */
    public function getCampConstructions(): Collection
    {
        return $this->campConstructions;
    }

    public function addCampConstruction(CampConstruction $campConstruction): static
    {
        if (!$this->campConstructions->contains($campConstruction)) {
            $this->campConstructions->add($campConstruction);
            $campConstruction->setCamp($this);
        }

        return $this;
    }

    public function removeCampConstruction(CampConstruction $campConstruction): static
    {
        if ($this->campConstructions->removeElement($campConstruction)) {
            // set the owning side to null (unless already changed)
            if ($campConstruction->getCamp() === $this) {
                $campConstruction->setCamp(null);
            }
        }

        return $this;
    }

    /**
     * @param string $buildingName
     * @return Collection<CampConstruction>
     */
    public function getCurrentBuildingConstructions(string $buildingName): Collection
    {
        return $this->getCampConstructions()->filter(
            fn(CampConstruction $construction) => $construction->getBuildingName() === $buildingName
        );
    }

    public function getNextLevelForBuilding(string $buildingName): int
    {
        $currentConstructions = $this->getCurrentBuildingConstructions($buildingName);
        if ($currentConstructions->isEmpty()) {
            return ($this->getBuilding($buildingName)?->getLevel() ?? 0) + 1;
        }

        return $currentConstructions->last()->getLevel() + 1;
    }


    public function addNewConstruction(string $buildingName, int $level, int $buildTime): CampConstruction
    {

        $construction = new CampConstruction();
        $construction->setLevel($level);
        $construction->setBuildingName($buildingName);

        $lastConstruction = $this->getCampConstructions()->last();

        $currentTime = $lastConstruction ? $lastConstruction->getCompletedAt() : new DateTimeImmutable();
        $construction->setStartedAt($currentTime);
        $currentTime = $currentTime->add(new DateInterval("PT{$buildTime}S"));

        $construction->setCompletedAt($currentTime);
        $this->addCampConstruction($construction);

        return $construction;
    }


    public function adjustConstructionQueue(?int $timestamp = null): void
    {
        // Recalculate build times.
        $iterator = $this->campConstructions->getIterator();
        $iterator->uasort(fn($a, $b) => $a->getStartedAt() <=> $b->getStartedAt());
        $previousCompletedAt = null;
        $currentTime = $timestamp ? (new DateTimeImmutable('@' . $timestamp)) : new DateTimeImmutable();
        foreach ($this->campConstructions as $constr) {
            /** @var CampConstruction $constr */



            $buildTime = $constr->getCompletedAt()->getTimestamp() - $constr->getStartedAt()->getTimestamp();
            $constr->setStartedAt($previousCompletedAt ?? $currentTime);
            $completedTime = $currentTime->add(new DateInterval("PT{$buildTime}S"));
            $constr->setCompletedAt($completedTime);


            $previousCompletedAt = $constr->getCompletedAt();
        }

    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): Camp
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): Camp
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function dequeueConstruction(CampConstruction $construction): Camp
    {
        $this->campConstructions->removeElement($construction);

        return $this;
    }

}

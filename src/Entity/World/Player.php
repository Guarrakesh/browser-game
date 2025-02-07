<?php

namespace App\Entity\World;

use App\Modules\Planet\Model\Entity\Planet;
use App\Repository\PlayerRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $userId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $joinedAt = null;

    /**
     * @var Collection<int, Planet>
     */
    #[ORM\OneToMany(targetEntity: Planet::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $planets;

    #[ORM\Column(nullable: true)]
    #[Timestampable]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->planets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getJoinedAt(): ?\DateTimeInterface
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTimeInterface $joinedAt): static
    {
        $this->joinedAt = $joinedAt;

        return $this;
    }

    /**
     * @return Collection<int, Planet>
     *
     */
    #[Ignore]
    public function getPlanets(): Collection
    {
        return $this->planets;
    }

    public function addPlanet(Planet $planet): static
    {
        if (!$this->planets->contains($planet)) {
            $this->planets->add($planet);
        }

        return $this;
    }

    public function removePlanet(Planet $planet): static
    {
        if ($this->planets->removeElement($planet)) {
            // set the owning side to null (unless already changed)
            if ($planet->getPlayer() === $this) {
                $planet->setPlayer(null);
            }
        }

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): Player
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }




}

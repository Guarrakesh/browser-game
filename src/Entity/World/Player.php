<?php

namespace App\Entity\World;

use App\Repository\PlayerRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\UX\Turbo\Attribute\Broadcast;

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
     * @var Collection<int, Camp>
     */
    #[ORM\OneToMany(targetEntity: Camp::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $camps;

    #[ORM\Column(nullable: true)]
    #[Timestampable]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->camps = new ArrayCollection();
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
     * @return Collection<int, Camp>
     */
    public function getCamps(): Collection
    {
        return $this->camps;
    }

    public function addCamp(Camp $camp): static
    {
        if (!$this->camps->contains($camp)) {
            $this->camps->add($camp);
            $camp->setPlayer($this);
        }

        return $this;
    }

    public function removeCamp(Camp $camp): static
    {
        if ($this->camps->removeElement($camp)) {
            // set the owning side to null (unless already changed)
            if ($camp->getPlayer() === $this) {
                $camp->setPlayer(null);
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

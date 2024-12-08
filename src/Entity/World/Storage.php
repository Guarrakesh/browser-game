<?php

namespace App\Entity\World;

use App\Repository\StorageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StorageRepository::class)]
#[ORM\Table('camp_storage')]
class Storage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'storage', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Camp $camp = null;

    #[ORM\Column]
    private ?int $concrete = null;

    #[ORM\Column]
    private ?int $metals = null;

    #[ORM\Column]
    private ?int $circuits = null;

    #[ORM\Column]
    private ?int $food = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCamp(): ?Camp
    {
        return $this->camp;
    }

    public function setCamp(Camp $camp): static
    {
        $this->camp = $camp;

        return $this;
    }

    public function getConcrete(): ?int
    {
        return $this->concrete;
    }

    public function setConcrete(int $concrete): static
    {
        $this->concrete = $concrete;

        return $this;
    }

    public function getMetals(): ?int
    {
        return $this->metals;
    }

    public function setMetals(int $metals): static
    {
        $this->metals = $metals;

        return $this;
    }

    public function getCircuits(): ?int
    {
        return $this->circuits;
    }

    public function setCircuits(int $circuits): static
    {
        $this->circuits = $circuits;

        return $this;
    }

    public function getFood(): ?int
    {
        return $this->food;
    }

    public function setFood(?int $food): void
    {
        $this->food = $food;
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


}

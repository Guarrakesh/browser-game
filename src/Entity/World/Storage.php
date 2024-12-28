<?php

namespace App\Entity\World;

use App\Model\ResourcePack;
use App\Repository\StorageRepository;
use DateTimeImmutable;
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
    private int $concrete = 0;

    #[ORM\Column]
    private int $metals = 0;

    #[ORM\Column]
    private int $circuits = 0;

    #[ORM\Column]
    private int $food = 0;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCamp(): ?Camp
    {
        return $this->camp;
    }

    public function addResources(ResourcePack $pack, int $maxStorage): static
    {
        $this->concrete = min($maxStorage, $this->concrete + round($pack->getConcrete()));
        $this->metals  = min($maxStorage, $this->metals + round($pack->getMetals()));
        $this->circuits  = min($maxStorage, $this->circuits + round($pack->getCircuits()));
        $this->food = min($maxStorage, $this->food + round($pack->getFood()));

        return $this;
    }
    public function setCamp(Camp $camp): static
    {
        $this->camp = $camp;

        return $this;
    }

    public function getConcrete(): int
    {
        return $this->concrete;
    }

    public function setConcrete(int $concrete): static
    {
        $this->concrete = $concrete;

        return $this;
    }

    public function getMetals(): int
    {
        return $this->metals;
    }

    public function setMetals(int $metals): static
    {
        $this->metals = $metals;

        return $this;
    }

    public function getCircuits(): int
    {
        return $this->circuits;
    }

    public function setCircuits(int $circuits): static
    {
        $this->circuits = $circuits;

        return $this;
    }

    public function getFood(): int
    {
        return $this->food;
    }

    public function setFood(int $food): void
    {
        $this->food = $food;
    }


    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


}

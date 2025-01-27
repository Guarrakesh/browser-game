<?php

namespace App\Entity\World;

use App\Modules\Core\Entity\Planet;
use App\Object\ResourcePack;
use App\Repository\StorageRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: StorageRepository::class)]
#[ORM\Table('planet_storage')]
class Storage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'storage', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Planet $planet = null;

    #[ORM\Column]
    private int $concrete = 0;

    #[ORM\Column]
    private int $metals = 0;

    #[ORM\Column]
    private int $circuits = 0;

    #[ORM\Column]
    private int $food = 0;

    #[ORM\Column(nullable: true)]
    #[Timestampable]
    private ?DateTimeImmutable $updatedAt = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    #[Ignore]
    public function getPlanet(): ?Planet
    {
        return $this->planet;
    }

    public function addResources(ResourcePack $pack, int $maxStorage): static
    {
        $this->concrete = max(0, min($maxStorage, $this->concrete + round($pack->getConcrete())));
        $this->metals = max(0, min($maxStorage, $this->metals + round($pack->getMetals())));
        $this->circuits = max(0, min($maxStorage, $this->circuits + round($pack->getCircuits())));
        $this->food = max(0, min($maxStorage, $this->food + round($pack->getFood())));

        return $this;
    }

    public function setPlanet(Planet $planet): static
    {
        $this->planet = $planet;

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

    public function containResources(ResourcePack $pack): bool
    {
        return $pack->getConcrete() <= $this->concrete
            && $pack->getMetals() <= $this->metals
            && $pack->getCircuits() <= $this->circuits
            && $pack->getFood() <= $this->food;

    }

    public function getAsPack(): ResourcePack
    {
        return new ResourcePack($this->concrete, $this->metals, $this->circuits, $this->food);
    }


}

<?php

namespace App\Modules\Planet\Model;

use App\Modules\Shared\Model\ResourcePack;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Clock\Clock;

/**
 * Storage is a Value Object and a child for the Planet Aggregate.
 */
#[ORM\Embeddable]
class Storage
{
    private const INITIAL_MAX_STORAGE = 1000;

    #[ORM\Column(options: ['default' => 0])]
    private int $concrete = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $metals = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $circuits = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $food = 0;

    #[ORM\Column(nullable: true)]
    #[Timestampable]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(int $initialStorage)
    {
        $this->addResources(ResourcePack::fromIdentity($initialStorage), $initialStorage);
    }

    public function addResources(ResourcePack $pack, int $maxStorage, ?DateTimeImmutable $at = null,): static
    {

        $this->concrete = max(0, min($maxStorage, $this->concrete + round($pack->getConcrete())));
        $this->metals = max(0, min($maxStorage, $this->metals + round($pack->getMetals())));
        $this->circuits = max(0, min($maxStorage, $this->circuits + round($pack->getCircuits())));
        $this->food = max(0, min($maxStorage, $this->food + round($pack->getFood())));

        $this->updatedAt = $at ?? Clock::get()->now();

        return $this;
    }

    public function subtractResources(ResourcePack $pack, int $maxStorage, ?DateTimeImmutable $at = null): static
    {
        return $this->addResources($pack->multiply(-1), $maxStorage);
    }


    public function getConcrete(): int
    {
        return $this->concrete;
    }

    public function getMetals(): int
    {
        return $this->metals;
    }

    public function getCircuits(): int
    {
        return $this->circuits;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
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

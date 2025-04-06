<?php

namespace App\Modules\Planet\Model\Entity\Drone;

use App\Modules\Planet\Model\Entity\Planet;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Represent an allocation of one or more drones to a planet pool (Control Hub, Resource Mine, Starter Ship)
 */
#[Entity]
#[ORM\UniqueConstraint(fields: ['planet', 'pool'])]
class DroneAllocation
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Planet::class, inversedBy: 'droneAllocations')]
    private Planet $planet;

    #[ORM\Column]
    private DronePoolEnum $pool;

    #[ORM\Column]
    private int $amount;

    public function __construct(Planet $planet, DronePoolEnum $pool, int $amount)
    {
        $this->planet = $planet;
        $this->pool = $pool;
        $this->amount = $amount;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getPool(): DronePoolEnum
    {
        return $this->pool;
    }

    public function getId(): ?int
    {
        return $this->id;
    }




}
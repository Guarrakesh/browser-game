<?php

namespace App\Planet\Domain\Repository;


use App\Planet\Domain\Entity\Drone\DroneAllocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @template-extends  ServiceEntityRepository<DroneAllocation>
 */
interface DroneAllocationRepositoryInterface
{
    /** @return DroneAllocation[] */
    public function findByPlanet(int $planetId): array;
}
<?php

namespace App\Planet\Infrastructure\Repository;

use App\Planet\Domain\Entity\Drone\DroneAllocation;
use App\Planet\Domain\Repository\DroneAllocationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @template-extends  ServiceEntityRepository<DroneAllocation>
 */
class DroneAllocationRepository extends ServiceEntityRepository implements DroneAllocationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DroneAllocation::class);
    }

    /** @return DroneAllocation[] */
    public function findByPlanet(int $planetId): array
    {
        $allocations = $this->findBy(['planet' => $planetId]);

        $ret = [];
        foreach ($allocations as $allocation) {
            $ret[$allocation->getPool()->value] = $allocation;
        }

        return $ret;
    }





}

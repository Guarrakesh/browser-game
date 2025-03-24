<?php

namespace App\Modules\Planet\Repository;

use App\Modules\Planet\Model\Entity\Drone\DroneAllocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @template-extends  ServiceEntityRepository<DroneAllocation>
 */
class DroneAllocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DroneAllocation::class);
    }

    /** @return DroneAllocation[] */
    public function findByPlanet(int $planetId): array
    {
        return $this->findBy(['planet' => $planetId]);
    }





}

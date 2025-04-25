<?php

namespace App\Planet\Domain\Repository;


use App\Planet\Domain\ValueObject\DroneQueue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @template-extends ServiceEntityRepository<DroneQueueJob>
 */
interface DroneQueueRepositoryInterface
{
    public function getDroneQueue(?int $planetId = null): DroneQueue;
}
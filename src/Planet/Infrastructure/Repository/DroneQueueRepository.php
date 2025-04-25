<?php

namespace App\Planet\Infrastructure\Repository;

use App\Planet\Domain\Entity\Drone\DroneQueueJob;
use App\Planet\Domain\Repository\DroneQueueRepositoryInterface;
use App\Planet\Domain\ValueObject\DroneQueue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<DroneQueueJob>
 */
class DroneQueueRepository extends ServiceEntityRepository implements DroneQueueRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DroneQueueJob::class);
    }

    public function getDroneQueue(?int $planetId = null): DroneQueue
    {
        $builder = $this->createQueryBuilder('dqj')
            ->andWhere('dqj.planetId = :planet')
            ->andWhere('dqj.processed = false AND dqj.cancelledAt IS NULL')
            ->setParameters(new ArrayCollection([
                new Parameter('planet', $planetId),
            ]))->getQuery()
        ;


        return new DroneQueue($builder->getResult());

    }


}
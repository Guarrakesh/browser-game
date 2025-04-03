<?php

namespace App\Modules\Planet\Repository;

use App\Modules\Planet\Model\DroneQueue;
use App\Modules\Planet\Model\Entity\Drone\DroneQueueJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<DroneQueueJob>
 */
class DroneQueueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DroneQueueJob::class);
    }

    public function getDroneQueue(int $planetId = null): DroneQueue
    {
        $builder = $this->createQueryBuilder('dqj')
            ->andWhere('dqj.planetId = :planet')
            ->andWhere('dqj.processed = false AND dqj.cancelledAt IS NULL')
            ->setParameters(new ArrayCollection([
                new Parameter('planet', $planetId),
            ]))->getQuery()
            ->enableResultCache();


        return new DroneQueue($builder->getResult());

    }


}
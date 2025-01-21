<?php

namespace App\Repository;

use App\Entity\World\Queue\Queue;
use App\Entity\World\Queue\ResearchQueueJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResearchQueueJob>
 */
class ResearchQueueJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResearchQueueJob::class);
    }

    //    /**
    //     * @return ResearchQueueJob[] Returns an array of ResearchQueueJob objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ResearchQueueJob
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function getResearchQueue(\App\Entity\World\Player $player)
    {
        $jobs = $this->createQueryBuilder('rqj')
            ->leftJoin('rqj.player', 'p')
            ->andWhere('p.id = :playerId')
            ->setParameter('playerId', $player->getId());

        return new Queue($jobs->getQuery()->getResult());

    }
}

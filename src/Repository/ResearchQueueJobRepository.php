<?php

namespace App\Repository;

use App\Entity\World\Player;
use App\Entity\World\Queue\Queue;
use App\Entity\World\Queue\ResearchQueueJob;
use App\Modules\Core\Entity\Planet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

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
    /**
     * @throws Exception
     */
    public function getResearchQueue(Player $player, ?Planet $planet = null, ?int $timestamp = null): Queue
    {
        $builder = $this->createQueryBuilder('rqj')
            ->andWhere('rqj.player = :player')
            ->andWhere('rqj.completedAt > :timestamp AND rqj.cancelledAt IS NULL')
            ->setParameters(new ArrayCollection([
                new Parameter('player', $player),
                new Parameter('timestamp', new \DateTimeImmutable('@' . ($timestamp ?? time())))
            ]));

        if ($planet) {
            $builder->andWhere('rqj.planet = :planet')
                ->setParameter('planet', $planet);
        }

        return new Queue($builder->getQuery()->getResult());

    }


    /** @return array<ResearchQueueJob> */
    public function getCompletedResearches(int $timestamp, ?Player $player = null): array
    {
        $builder = $this->createQueryBuilder('rqj');

        if ($player) {
            $builder->andWhere('rqj.player = :player')
                ->setParameter('player', $player);
        }

        return $builder
            ->andWhere('rqj.completedAt <= :timestamp')
            ->andWhere('rqj.cancelledAt IS NULL')
            ->setParameter('timestamp', new \DateTimeImmutable('@' . $timestamp))
            ->getQuery()
            ->getResult();
    }
}

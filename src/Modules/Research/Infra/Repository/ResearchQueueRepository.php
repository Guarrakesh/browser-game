<?php

namespace App\Modules\Research\Infra\Repository;

use App\Entity\World\Player;
use App\Modules\Research\Model\Entity\ResearchQueueJob;
use App\Modules\Research\Model\ResearchQueue;
use App\Modules\Shared\Model\Queue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<ResearchQueueJob>
 */
class ResearchQueueRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResearchQueueJob::class);
    }

    /**
     * @throws Exception
     */
    public function getResearchQueue(int $playerId, int $planetId = null): ResearchQueue
    {
        $builder = $this->createQueryBuilder('rqj')
            ->andWhere('rqj.playerId = :player')
            ->andWhere('rqj.processed = false AND rqj.cancelledAt IS NULL')
            ->setParameters(new ArrayCollection([
                new Parameter('player', $playerId),
            ]));

        if ($planetId) {
            $builder->andWhere('rqj.planetId = :planet')
                ->setParameter('planet', $planetId);
        }

        return new ResearchQueue($builder->getQuery()->getResult());

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

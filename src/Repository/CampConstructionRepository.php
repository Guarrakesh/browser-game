<?php

namespace App\Repository;

use App\Entity\World\Camp;
use App\Entity\World\Queue\CampConstruction;
use App\Entity\World\Queue\Queue;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use FrankProjects\UltimateWarfare\Entity\Construction;
use FrankProjects\UltimateWarfare\Entity\GameUnit;

/**
 * @extends ServiceEntityRepository<CampConstruction>
 */
class CampConstructionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampConstruction::class);
    }

    public function getCompletedConstructions(int $timestamp, ?Camp $camp = null): array
    {
        $builder = $this->createQueryBuilder('cc');
        $builder->leftJoin('cc.camp', 'c');

        if ($camp) {
            $builder->andWhere('cc.camp = :camp')->setParameter('camp', $camp);
        }

        return $builder->andWhere('cc.completedAt < :timestamp')
            ->setParameter('timestamp', (new DateTimeImmutable('@' . $timestamp)))
            ->getQuery()
            ->getResult();

    }

    public function getConstructionQueue(Camp $camp): Queue
    {
        $jobs = $this->createQueryBuilder('cc')
            ->leftJoin('cc.camp', 'c');

        return new Queue($jobs->getQuery()->getResult());
    }

    //    /**
    //     * @return CampConstruction[] Returns an array of CampConstruction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CampConstruction
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

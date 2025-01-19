<?php

namespace App\Repository;

use App\Entity\World\Camp;
use App\Entity\World\Fleet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fleet>
 */
class FleetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fleet::class);
    }

    /**
     * @param Camp $camp
     * @return array<Fleet>
     */
    public function getFleetsByCamp(Camp $camp): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.camp = :c')
            ->setParameter('c', $camp)
            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return Fleet[] Returns an array of Fleet objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Fleet
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
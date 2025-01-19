<?php

namespace App\Repository;

use App\Entity\World\Camp;
use App\Entity\World\CampShip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CampShip>
 */
class ShipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampShip::class);
    }

    /** @return array<CampShip> */
    public function getUngroupedShipsByCamp(Camp $camp): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.camp = :c')
            ->andWhere('s.fleet is NULL')
            ->setParameter('c', $camp)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return CampShip[] Returns an array of CampShip objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CampShip
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

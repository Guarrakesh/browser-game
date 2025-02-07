<?php

namespace App\Repository;

use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Planet\Model\Entity\PlanetShip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanetShip>
 */
class ShipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanetShip::class);
    }

    /** @return array<PlanetShip> */
    public function getUngroupedShipsByPlanet(Planet $planet): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.planet = :c')
            ->andWhere('s.fleet is NULL')
            ->setParameter('c', $planet)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return PlanetShip[] Returns an array of PlanetShip objects
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

    //    public function findOneBySomeField($value): ?PlanetShip
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

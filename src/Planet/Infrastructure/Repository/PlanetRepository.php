<?php

namespace App\Planet\Infrastructure\Repository;

use App\Planet\Domain\Entity\Planet;
use App\Planet\Domain\Repository\PlanetRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @template-extends ServiceEntityRepository<Planet>
 */
class PlanetRepository extends ServiceEntityRepository implements PlanetRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planet::class);
    }

    /** @return Planet[] */
    public function findByPlayer(int $playerId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.playerId =     :playerId')
            ->setParameter('playerId', $playerId)
            ->getQuery()
            ->getResult();
    }

    public function playerHasPlanets(int $playerId): bool
    {
        return $this->createQueryBuilder('p')
                ->select('count(p.id)')
                ->where('p.playerId = :playerId')
                ->setParameter('playerId', $playerId)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function playerHasPlanet(int $planetId, int $playerId): bool
    {
        return $this->createQueryBuilder('p')
                ->select('count(p.id)')
                ->andWhere('p.playerId = :playerId AND p.id = :planetId')
                ->setParameter('playerId', $playerId)
                ->setParameter('planetId', $planetId)
                ->getQuery()->getSingleScalarResult() > 0;

    }

}

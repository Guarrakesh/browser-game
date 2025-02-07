<?php

namespace App\Modules\Planet\Infra\Repository;

use App\Modules\Planet\Model\Entity\Planet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Planet>
 */
class PlanetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planet::class);
    }

    /** @return Planet[] */
    public function findByPlayer(int $playerId): array
    {
        return $this->findBy(['playerId' => $playerId]);
    }
}

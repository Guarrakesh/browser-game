<?php

namespace App\Repository;

use App\Entity\World\PlayerTech;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerTech>
 */
class PlayerTechRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerTech::class);
    }

    public function findByPlayer(int $playerId): ?PlayerTech
    {
        return $this->findOneBy(['player' => $playerId]);
    }
}

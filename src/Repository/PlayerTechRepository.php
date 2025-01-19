<?php

namespace App\Repository;

use App\Entity\World\Player;
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

    public function findByPlayer(Player $player): ?PlayerTech
    {
        return $this->findOneBy(['player' => $player]);
    }
}
<?php

namespace App\Modules\Research\Infra\Repository;

use App\Modules\Research\Model\Entity\PlayerTech;
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

    /** @return PlayerTech[] */
    public function findByPlayer(int $playerId): array
    {
        return $this->findBy(['playerId' => $playerId]);
    }

    /** @return array<string,PlayerTech> */
    public function findByPlayerAssociative(int $playerId): array
    {
        $techs =  $this->findByPlayer($playerId);
        $map = [];
        foreach ($techs as $tech) {
            $map[$tech->getTechName()] = $tech;
        }

        return $map;
    }
}

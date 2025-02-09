<?php

namespace App\Repository;

use App\Entity\Central\User;
use App\Entity\World\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Player>
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function findByUser(UserInterface $user): ?Player
    {
        /** @var User $user */
        return $this->findOneBy(['userId' => $user->getId()]);
    }

}

<?php

namespace App\Repository;

use App\Entity\World\Queue\PlanetConstruction;
use App\Entity\World\Queue\Queue;
use App\Modules\Core\Entity\Planet;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanetConstruction>
 */
class PlanetConstructionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanetConstruction::class);
    }


    public function getCompletedConstructions(int $timestamp, ?Planet $planet = null): array
    {
        $builder = $this->createQueryBuilder('cc');

        if ($planet) {
            $builder->andWhere('cc.planet = :planet')->setParameter('planet', $planet);
        }

        return $builder
            ->andWhere('cc.completedAt <= :timestamp')
            ->andWhere('cc.cancelledAt IS NULL')
            ->setParameter('timestamp', (new DateTimeImmutable('@' . $timestamp)))
            ->getQuery()
            ->getResult();

    }

    public function getConstructionQueue(Planet $planet, ?int $timestamp = null): Queue
    {
        $jobs = $this->createQueryBuilder('cc')
            ->andWhere('cc.planet = :planet')
            ->andWhere('cc.completedAt > :timestamp AND cc.cancelledAt IS NULL ')
            ->setParameters(new ArrayCollection([
                new Parameter('planet', $planet),
                new Parameter('timestamp', (new DateTimeImmutable('@' . ($timestamp ?? time())))),
            ]));

        return new Queue($jobs->getQuery()->getResult());
    }

    //    }
}

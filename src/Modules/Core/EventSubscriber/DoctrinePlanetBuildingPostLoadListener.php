<?php

namespace App\Modules\Core\EventSubscriber;

use App\Modules\Core\Entity\PlanetBuilding;
use App\ObjectRegistry\BuildingRegistry;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postLoad, method: 'postLoad', entityManager: 'world', entity: PlanetBuilding::class)]
class DoctrinePlanetBuildingPostLoadListener
{
    public function __construct(private readonly BuildingRegistry $registry)
    {
    }

    public function postLoad(PlanetBuilding $building, PostLoadEventArgs $event)
    {
        $definition = $building->getDefinition();
        if ($definition) {
            return;
        }

        $definition = $this->registry->find($building->getName());
        if (!$definition) {
            return;
        }
        $building->setDefinition($definition);

    }
}
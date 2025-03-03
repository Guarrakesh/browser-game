<?php

namespace App\Modules\Planet\Infra\EventSubscriber;

use App\Modules\Planet\Infra\Registry\BuildingRegistry;
use App\Modules\Planet\Model\Entity\BuildingDefinitionAwareInterface;
use App\Modules\Planet\Model\Entity\PlanetBuilding;
use App\Modules\Planet\Model\Entity\PlanetConstruction;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postLoad, method: 'postLoad', entityManager: 'world', entity: PlanetConstruction::class)]
#[AsEntityListener(event: Events::postLoad, method: 'postLoad', entityManager: 'world', entity: PlanetBuilding::class)]
readonly class DoctrinePlanetBuildingPostLoadListener
{
    public function __construct(private BuildingRegistry $registry)
    {
    }

    public function postLoad(BuildingDefinitionAwareInterface $building, PostLoadEventArgs $event)
    {
        $definition = $building->getDefinition();
        if ($definition) {
            return;
        }

        $definition = $this->registry->find($building->getBuildingName());
        if (!$definition) {
            return;
        }
        $building->setDefinition($definition);

    }
}
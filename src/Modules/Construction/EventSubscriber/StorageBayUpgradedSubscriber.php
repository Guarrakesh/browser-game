<?php

namespace App\Modules\Construction\EventSubscriber;

use App\Constants;
use App\Modules\Construction\Event\ConstructionCompletedEvent;
use App\Modules\Construction\Message\ConstructionCompleted;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class StorageBayUpgradedSubscriber implements EventSubscriberInterface
{

    public function onConstructionCompleted(ConstructionCompletedEvent $event)
    {

        if ($event->getConstruction()->getBuildingName() !== Constants::STORAGE_BAY) {
            return;
        }

        /**
         * TODO: Does it make sense to move this logic somewhere else? Maybe not now
         * This logic to update the maxStorage can also be run if some **effect** is activated/expired.
         * Or when the Storage is destroyed in a battle or demolished by the player.
        */
        $planet = $event->getConstruction()->getPlanet();
        $storageBayConfig = $planet->getBuilding(Constants::STORAGE_BAY)->getDefinition();

        $storageIncreaseFactor = $storageBayConfig->findParameter('storage_increase_factor');
        $baseStorage = $storageBayConfig->findParameter('base_storage');

        $bay = $planet->getBuilding(Constants::STORAGE_BAY);
        $level = min($bay->getLevel(), $storageBayConfig->getMaxLevel());

        $planet->getStorage()->setMaxStorage($baseStorage * ($storageIncreaseFactor ** ($level - 1)));

    }

    public static function getSubscribedEvents()
    {
        return [
            ConstructionCompletedEvent::class => ['onConstructionCompleted', 0],
        ];
    }
}
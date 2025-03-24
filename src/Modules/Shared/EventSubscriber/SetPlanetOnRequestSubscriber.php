<?php

namespace App\Modules\Shared\EventSubscriber;

use App\Exception\GameException;
use App\Modules\Planet\Repository\PlanetRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class  SetPlanetOnRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private PlanetRepository $planetRepository,
    )
    {
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $playerId = $request->attributes->get('playerId');
        if (!$playerId) {
            return;
        }

        $planetId = $request->get('planetId');
        if ($planetId) {
            if (!$this->planetRepository->playerHasPlanet($planetId, $playerId)) {
                throw new GameException("Invalid planet.");
            }
        }


        // If no ID, Get first village of the player
        $planetId = $this->planetRepository->findOneBy(['playerId' => $playerId], ['id' => 'ASC'])?->getId();
        $request->attributes->set('planetId', $planetId);

        return;

    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 5]
        ];
    }
}
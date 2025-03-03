<?php

namespace App\Modules\Shared\EventSubscriber;

use App\Engine\GameEngine;
use App\Exception\GameException;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Planet\Service\ResourceService;
use App\Modules\Shared\Repository\PlayerRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Stopwatch\Stopwatch;

readonly class SetPlayerOnRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security         $security,
                                private PlayerRepository $playerRepository,
    )
    {
    }

    public function onRequest(RequestEvent $event): void
    {

        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user) {
            return;
        }


        $request = $event->getRequest();

        $playerId = $request->attributes->get('playerId', $this->playerRepository->findByUser($user)->getId());
        if (!$playerId) {
            // TODO: handle banned playerId.
            throw new GameException("Invalid request: playerId not found");
        }

        $request->attributes->set('playerId', $playerId);

    }
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 6]
        ];
    }
}
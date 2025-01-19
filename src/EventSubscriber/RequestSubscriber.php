<?php

namespace App\EventSubscriber;

use App\Engine\GameEngine;
use App\Repository\PlayerRepository;
use App\Resource\ResourceService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security,
                                private PlayerRepository $playerRepository,
                                private ResourceService $resourceService,
                                private GameEngine $gameEngine
    )
    {
    }

    public function onRequest(RequestEvent $event): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $player = $this->playerRepository->findByUser($user);
        if (!$player) {
            // TODO: handle banned player.
            throw new \LogicException("Player not found");
        }

        $event->getRequest()->attributes->set('player', $player);

        $this->resourceService->updateResourcesForPlayer($player);

        $this->gameEngine->run();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 0]
        ];
    }
}
<?php

namespace App\Modules\Shared\EventSubscriber;

use App\Engine\GameEngine;
use App\Modules\Planet\Service\ResourceService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Stopwatch\Stopwatch;

readonly class  RunEngineOnRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
                                private ResourceService  $resourceService,
                                private GameEngine       $gameEngine,
                                private Stopwatch $stopwatch
    )
    {
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $stopwatch = $this->stopwatch;
        $playerId = $event->getRequest()->attributes->get('playerId');
        if ($playerId) {
            $stopwatch->start('Update Player Resources', 'Engine');
                $this->resourceService->updateResourcesForPlayer($playerId);
            $stopwatch->stop('Update Player Resources');
        }

        $event = $stopwatch->start('Run Engine','Engine');
        $this->gameEngine->run();
        $stopwatch->stop($event->getName());

    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 0]
        ];
    }
}
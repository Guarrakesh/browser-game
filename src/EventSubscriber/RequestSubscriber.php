<?php

namespace App\EventSubscriber;

use App\Construction\ConstructionService;
use App\Engine\GameEngine;
use App\Engine\Processor\ConstructionProcessor;
use App\Resource\ResourceService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security,
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
        $this->resourceService->updateResourcesForUser($user);

        $this->gameEngine->run();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 0]
        ];
    }
}
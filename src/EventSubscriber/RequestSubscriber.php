<?php

namespace App\EventSubscriber;

use App\Service\ResourceService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security, private ResourceService $resourceService)
    {
    }

    public function onRequest(RequestEvent $event): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }
        $this->resourceService->updateResourcesForUser($user);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 0]
        ];
    }
}
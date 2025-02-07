<?php

namespace App\Modules\Shared\EventSubscriber;

use App\Engine\GameEngine;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Planet\Service\ResourceService;
use App\Repository\PlayerRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Stopwatch\Stopwatch;

readonly class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security         $security,
                                private PlayerRepository $playerRepository,
                                private ResourceService  $resourceService,
                                private GameEngine       $gameEngine,
                                private PlanetRepository $planetRepository,
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
        $this->setRequestAttributes($event->getRequest());

        $player = $event->getRequest()->attributes->get('player');
        if ($player) {
            $stopwatch->start('Update Player Resources', 'Engine');
            $this->resourceService->updateResourcesForPlayer($player);
            $stopwatch->stop('Update Player Resources');
        }
        $event = $stopwatch->start('Run Engine','Engine');
        $this->gameEngine->run();
        $stopwatch->stop($event->getName());
    }

    private function setRequestAttributes(Request $request): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $player = $request->attributes->get('player',  $this->playerRepository->findByUser($user));
        if (!$player) {
            // TODO: handle banned player.
            throw new \LogicException("Player not found");
        }

        $request->attributes->set('player', $player);

        $planetId = $request->query->get('planetId');$planet = null;


        if (!$planetId) {
            // If no ID, Get first village of the player
            $planetId = $this->planetRepository->findOneBy(['playerId' => $player->getId()], ['id' => 'ASC'])->getId();
        }

        if (!$planetId) {
            throw new NotFoundHttpException("Player has no planets");
        }

        $request->attributes->set('planetId', $planetId);


    }
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 0]
        ];
    }
}
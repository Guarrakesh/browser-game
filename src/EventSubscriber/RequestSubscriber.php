<?php

namespace App\EventSubscriber;

use App\Engine\GameEngine;
use App\Modules\Core\DTO\PlanetDTO;
use App\Modules\Core\Repository\PlanetRepository;
use App\Repository\PlayerRepository;
use App\Service\ResourceService;
use AutoMapper\AutoMapperInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class RequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security         $security,
                                private PlayerRepository $playerRepository,
                                private ResourceService  $resourceService,
                                private GameEngine       $gameEngine,
                                private PlanetRepository $planetRepository, private AutoMapperInterface $autoMapper
    )
    {
    }

    public function onRequest(RequestEvent $event): void
    {
        $this->setRequestAttributes($event->getRequest());

        $player = $event->getRequest()->attributes->get('player');
        if ($player) {
            $this->resourceService->updateResourcesForPlayer($player);
        }
        $this->gameEngine->run();
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

        $planetId = $request->query->get('planetId');
        $planet = null;
        if ($planetId) {
            $planet = $this->planetRepository->findOneBy(['player' => $player, 'id' => $planetId]);
        }

        if (!$planet) {
            // If no ID, Get first village of the player
            $planet = $this->planetRepository->findOneBy(['player' => $player], ['id' => 'ASC']);
        }

        if (!$planet) {
            throw new NotFoundHttpException("Player has no planets");
        }

        $request->attributes->set('planet', $this->autoMapper->map($planet, new PlanetDTO()));


    }
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 0]
        ];
    }
}
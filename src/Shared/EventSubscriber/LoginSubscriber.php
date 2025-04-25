<?php

namespace App\Shared\EventSubscriber;

use App\Entity\World\Player;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

readonly class LoginSubscriber implements EventSubscriberInterface
{

    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }

    public function onLoginSuccess(LoginSuccessEvent $event)
    {
        $user = $event->getUser();

        $manager = $this->managerRegistry->getManager('world');
        $player = $manager->getRepository(Player::class)->findByUser($user);

        if ($player) {
            return;
        }
        $player = new Player();
        $player->setUserId($user->getId());
        $player->setJoinedAt(new \DateTimeImmutable());
        $manager->persist($player);
        $manager->flush();


    }

    public static function getSubscribedEvents()
    {
        return [
            LoginSuccessEvent::class => ['onLoginSuccess', 0]
        ];
    }
}
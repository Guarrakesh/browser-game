<?php

namespace App\Controller\World;

use App\Entity\Central\User;
use App\Entity\World\Player;
use App\Repository\PlayerRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class WorldController extends AbstractController
{
    #[Route('/world', name: 'app_world')]
    public function index(): Response
    {
        return $this->render('world/index.html.twig', [
            'controller_name' => 'WorldController',
        ]);
    }

    #[Route('/world/join', name: 'app_world_world_join')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function join(ManagerRegistry $registry, Security $security): Response
    {
        /** @var User $user */
        $user = $security->getUser();
        $manager = $registry->getManager('world');
        $playerRepository = $manager->getRepository(Player::class);

        $player = $playerRepository->findOneBy(['userId' => $user->getId()]);
        if ($player) {
            return $this->redirectToRoute('app_world');
        }

        $player = new Player();
        $player->setJoinedAt(new DateTimeImmutable());
        $player->setUserId($user->getId());
        $manager->persist($player);
        $manager->flush();

        $this->addFlash('success', 'User joined the world!');

        return $this->redirect('app_world_world_join');

    }
}

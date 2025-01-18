<?php

namespace App\Controller\World;

use App\Camp\CampFacade;
use App\Camp\CampSetupService;
use App\Constants;
use App\Construction\ConstructionService;
use App\Controller\World\Building\BuildingControllerInterface;
use App\Entity\World\Camp;
use App\Entity\World\Player;
use App\Exception\GameException;
use App\Repository\CampRepository;
use App\Repository\PlayerRepository;
use App\Resource\ResourceService;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/camp')]
class CampController extends AbstractController
{

    /**
     * @param PlayerRepository $playerRepository
     * @param CampRepository $campRepository
     * @param ServiceLocator<BuildingControllerInterface> $buildingControllers
     */
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly CampRepository   $campRepository,
        #[AutowireLocator(BuildingControllerInterface::class, defaultIndexMethod: 'getType')]
        private readonly ServiceLocator   $buildingControllers,
    )
    {
    }

    #[Route('/', name: 'camp', methods: ['GET'])]
    public function index(Request $request, CampFacade $campFacade)
    {
        $camp = $this->getCamp($request);

        return $this->render('camp/index.html.twig', [
            'camp' => $camp,
        ]);
    }


    #[Route('/new', name: 'camp_new')]
    public function newCamp(
        ManagerRegistry $managerRegistry,
        CampRepository  $campRepository, CampSetupService $campSetupService): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw new Exception("Invalid user");
        }
        $player = $this->playerRepository->findOneBy(['userId' => $user->getId()]);
        if (!$player) {
            $player = new Player();
            $player->setUserId($user->getId());
            $player->setJoinedAt(new \DateTime());

            $managerRegistry->getManager('world')->persist($player);
            $managerRegistry->getManager('world')->flush();
        }
        $camp = $campRepository->findOneBy(['player' => $player]);
        if ($camp) {
            return $this->redirectToRoute('camp');
        }

        $camp = $campSetupService->createCamp($player);

        return $this->redirectToRoute('camp', ['id' => $camp->getId()]);

    }

    #[Route('/building/{name}', name: 'camp_building', methods: ['GET'])]
    public function buildingIndex(Request $request, string $name): Response
    {
        $camp = $this->getCamp($request);

        $building = $camp->getBuilding($name);

        if (!$building) {
            return $this->redirectToRoute('camp');
        }
        if (!$this->buildingControllers->has($building->getName())) {
            $this->addFlash('error', "Building {$building->getName()} not found");
            return $this->redirectToRoute('camp');
        }

        $controller = $this->buildingControllers->get($building->getName());
        if (!$controller) {
            return $this->render('camp/buildings/' . $building->getName() . '/index.html.twig', [
                'building' => $building,
                'camp' => $camp
            ]);
        }

        return $controller->handle($request, $building);
    }

    #[Route('/building/build/{name}', name: 'camp_building_build', methods: ['GET'])]
    public function build(Request $request, string $name,  ConstructionService $service)
    {
        $camp = $this->getCamp($request);

        try {
            $service->enqueueConstruction($camp, $name);
        } catch (GameException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }
        return $this->redirectToRoute('camp_building', ['name' => Constants::CONTROL_HUB]);
    }

    private function getCamp(Request $request): Camp
    {
        $player = $this->playerRepository->findOneBy(['userId' => $this->getUser()?->getId()]);
        $campId = $request->query->get('campId');
        $camp = null;
        if ($campId) {
            $camp = $this->campRepository->findOneBy(['player' => $player, 'id' => $campId]);
        }

        if (!$camp) {
            // If no ID, Get first village of the player
            $camp = $this->campRepository->findOneBy(['player' => $player], ['id' => 'ASC']);
        }

        if (!$camp) {
            throw new NotFoundHttpException("Player has no camps");
        }

        return $camp;
    }


}

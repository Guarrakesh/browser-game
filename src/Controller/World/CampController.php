<?php

namespace App\Controller\World;

use App\Constants;
use App\Controller\World\Building\BuildingControllerInterface;
use App\Entity\World\Camp;
use App\Entity\World\Player;
use App\Repository\CampRepository;
use App\Repository\PlayerRepository;
use App\Service\BuildingConfigurationService;
use App\Service\Camp\CampSetupService;
use App\Service\ResourceService;
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
        private readonly ResourceService $resourceService,
        #[AutowireLocator(BuildingControllerInterface::class, defaultIndexMethod: 'getType')]
        private readonly ServiceLocator   $buildingControllers
    )
    {
    }

    #[Route('/', name: 'camp', methods: ['GET'])]
    public function index(Request $request, BuildingConfigurationService $buildingConfigurationService)
    {
        $camp = $this->getCamp($request);
        $production = $this->resourceService->getHourlyProduction($camp);

        return $this->render('camp/index.html.twig', [
            'camp' => $camp,
            'production' => $production,
            'maxStorage' => $camp->getMaxStorage($buildingConfigurationService->getBuildingConfigProvider(Constants::STORAGE_BAY))
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

    #[Route('/building/{type}', name: 'camp_building', methods: ['GET'])]
    public function buildingIndex(Request $request, string $type): Response
    {
        $camp = $this->getCamp($request);

        $building = $camp->getBuilding($type);
        if (!$building) {
            return $this->redirectToRoute('camp');
        }
        $controller = $this->buildingControllers->get($building->getType());
        if (!$controller) {
            return $this->render('camp/buildings/' . $building->getType() . '/index.html.twig', [
                'building' => $building,
                'camp' => $camp
            ]);
        }

        return $controller->handle($request, $building);
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

<?php

namespace App\Controller\World;


use App\Entity\World\Player;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Planet\Service\PlanetOverviewService;
use App\Repository\PlayerRepository;
use App\Service\PlanetSetupService;
use App\Service\ValueResolver\PlanetValueResolver;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/planet')]
class PlanetController extends AbstractController
{

    /**
     * @param PlayerRepository $playerRepository
     * @param PlanetRepository $planetRepository
     */
    public function __construct(
        private readonly PlayerRepository $playerRepository
    )
    {
    }

    #[Route('/', name: 'planet', methods: ['GET'])]
    public function index(Request $request, #[ValueResolver(PlanetValueResolver::class)] int $planetId, PlanetOverviewService $planetOverviewService)
    {

        $dto = $planetOverviewService->getPlanetOverview($planetId);
        return $this->render('planet/index.html.twig', [
            'planet' => $dto,
        ]);
    }


    #[Route('/new', name: 'planet_new')]
    public function newPlanet(
        ManagerRegistry  $managerRegistry,
        PlanetRepository $planetRepository, PlanetSetupService $planetSetupService): Response
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
        $planet = $planetRepository->findOneBy(['player' => $player]);
        if ($planet) {
            return $this->redirectToRoute('planet');
        }

        $planet = $planetSetupService->createPlanet($player);

        return $this->redirectToRoute('planet', ['id' => $planet->getId()]);

    }

//    #[Route('/building/{name}', name: 'planet_building', methods: ['GET', 'POST'])]
//    public function buildingIndex(Request $request, string $name): Response
//    {
//        $planet = $this->getPlanet($request);
//
//        $building = $planet->getBuilding($name);
//
//        if (!$building) {
//            return $this->redirectToRoute('planet');
//        }
//        if (!$this->buildingControllers->has($building->getName())) {
//            $this->addFlash('error', "ConstructionDTO {$building->getName()} not found");
//            return $this->redirectToRoute('planet');
//        }
//
//        $controller = $this->buildingControllers->get($building->getName());
//        if (!$controller) {
//            return $this->render('planet/buildings/' . $building->getName() . '/index.html.twig', [
//                'building' => $building,
//                'planet' => $planet
//            ]);
//        }
//
//        return $controller->handle($request, $building);
//    }

//    private function getPlanet(Request $request): Planet
//    {
//        $player = $this->playerRepository->findOneBy(['userId' => $this->getUser()?->getId()]);
//        $planetId = $request->query->get('planetId');
//        $planet = null;
//        if ($planetId) {
//            $planet = $this->planetRepository->findOneBy(['player' => $player, 'id' => $planetId]);
//        }
//
//        if (!$planet) {
//            // If no ID, Get first village of the player
//            $planet = $this->planetRepository->findOneBy(['player' => $player], ['id' => 'ASC']);
//        }
//
//        if (!$planet) {
//            throw new NotFoundHttpException("Player has no planets");
//        }
//
//        return $planet;
//    }
//

}

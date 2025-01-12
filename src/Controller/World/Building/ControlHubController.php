<?php

namespace App\Controller\World\Building;

use App\Camp\BuildingConfigurationService;
use App\Construction\ConstructionService;
use App\Entity\World\CampBuilding;
use App\Exception\GameException;
use App\Repository\CampConstructionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControlHubController extends AbstractController implements BuildingControllerInterface
{

    public function __construct(
        private readonly CampConstructionRepository   $campConstructionRepository,
        private readonly ConstructionService          $constructionService,
        private readonly BuildingConfigurationService $buildingConfigurationService,
        private readonly ManagerRegistry              $managerRegistry)
    {
    }

    public static function getType(): string
    {
        return 'control_hub';
    }

    /**
     * @throws \Exception
     */
    public function handle(Request $request, CampBuilding $building): Response
    {
        $configs = $this->buildingConfigurationService->getAllConfigs();

        $action = $request->get('action');

        try {
            if ($action === 'cancel_construction') {
                $construction = $this->campConstructionRepository->find($request->get('payload'));
                if ($construction) {
                    $this->constructionService->cancelConstruction($construction);
                }
            }
        } catch (GameException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }


        $this->managerRegistry->getManager('world')->refresh($building);
        return $this->render('camp/buildings/control_hub/index.html.twig', [
            'building' => $building,
            'camp' => $building->getCamp(),
            'buildings' => $configs
        ]);


    }
}
<?php

namespace App\Controller\World\Building\ControlHub;

use App\Camp\BuildingConfigurationService;
use App\Constants;
use App\Construction\ConstructionService;
use App\Controller\World\Building\AbstractBuildingController;
use App\Controller\World\Building\BuildingActionInterface;
use App\Controller\World\Building\BuildingControllerInterface;
use App\Entity\World\CampBuilding;
use App\Exception\GameException;
use App\Repository\CampConstructionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControlHubController extends AbstractBuildingController
{

    /**
     * @param ServiceLocator<BuildingActionInterface> $actions
     */
    public function __construct(
        #[AutowireLocator(Constants::CONTROL_HUB . '.actions')] private ServiceLocator $actions,
        private readonly CampConstructionRepository   $campConstructionRepository,
        private readonly ConstructionService          $constructionService,
        private readonly BuildingConfigurationService $buildingConfigurationService,
        private readonly ManagerRegistry              $managerRegistry)
    {
    }

    public static function getType(): string
    {
        return Constants::CONTROL_HUB;
    }

    /**
     * @throws \Exception
     */
    public function handle(Request $request, CampBuilding $building): Response
    {
        $configs = $this->buildingConfigurationService->getAllConfigs();

        $action = $request->get('action');

        try {
            if ($action && $this->actions->has($action)) {
                $action = $this->actions->get($action);
                $response = $action->execute($request, $building);
                if ($response) {
                    return $response;
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
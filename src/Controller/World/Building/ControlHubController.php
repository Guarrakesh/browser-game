<?php

namespace App\Controller\World\Building;

use App\Entity\World\CampBuilding;
use App\Service\BuildingConfigurationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControlHubController extends AbstractController implements BuildingControllerInterface
{

    public function __construct(private readonly BuildingConfigurationService $buildingConfigurationService)
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
        return $this->render('camp/buildings/control_hub/index.html.twig', [
            'building' => $building,
            'camp' => $building->getCamp(),
            'buildings' => $configs
        ]);
    }
}
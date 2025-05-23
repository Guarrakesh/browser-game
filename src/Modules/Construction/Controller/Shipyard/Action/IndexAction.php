<?php

namespace App\Modules\Construction\Controller\Shipyard\Action;

use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Construction\Controller\BuildingActionInterface;
use App\Planet\Domain\Entity\Planet;
use App\Planet\ViewModel\BuildingViewModel;
use App\Shared\Constants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[AsBuildingAction(Constants::RESEARCH_CENTER)]
#[Route('/shipyard', name: 'shipyard_center_index', methods: ['GET'])]
class IndexAction extends AbstractController implements BuildingActionInterface
{


    public function __invoke(Request $request, Planet $planet): BuildingViewModel
    {
        $building = $planet->getBuilding(Constants::SHIPYARD);
        $viewModel = new BuildingViewModel($building);
        $response = $this->render('planet/buildings/shipyard/index.html.twig', [
            'planet' => $building->getPlanet(),
            'building' => $building,
            'shipRegistry' => $this->registry,
        ]);

        $viewModel->response = $response;
        return $viewModel;
    }



    public static function getName(): string
    {
        return ActionEnum::IndexAction->value;
    }
}
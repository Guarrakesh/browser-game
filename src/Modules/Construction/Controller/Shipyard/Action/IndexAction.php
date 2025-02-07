<?php

namespace App\Modules\Construction\Controller\Shipyard\Action;

use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Construction\Controller\BuildingActionInterface;
use App\Modules\Planet\Infra\Registry\ResearchTechRegistry;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Planet\ViewModel\BuildingViewModel;
use App\Modules\Shared\Constants;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[AsBuildingAction(Constants::RESEARCH_CENTER)]
#[Route('/shipyard', name: 'shipyard_center_index', methods: ['GET'])]
class IndexAction extends AbstractController implements BuildingActionInterface
{

    public function __construct(ContainerInterface                    $container,
                                private readonly ResearchTechRegistry $registry,
    )
    {
        parent::__construct($container);
    }

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
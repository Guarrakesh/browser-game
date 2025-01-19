<?php

namespace App\Controller\World\Building\ResearchCenter\Action;

use App\Constants;
use App\Controller\World\Building\AbstractBuildingAction;
use App\Controller\World\Building\AsBuildingAction;
use App\Entity\World\CampBuilding;
use App\Entity\World\PlayerTech;
use App\Model\ViewModel\BuildingViewModel;
use App\Model\ViewModel\ResearchCenterViewModel;
use App\Object\ResearchTech;
use App\ObjectRegistry\ResearchTechRegistry;
use App\Repository\PlayerTechRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;


#[AsBuildingAction(Constants::RESEARCH_CENTER)]
class ListTechsAction extends AbstractBuildingAction
{

    public function __construct(ContainerInterface $container,
                                private readonly ResearchTechRegistry $registry,
                                private readonly PlayerTechRepository $playerTechRepository,
    )
    {
        parent::__construct($container);
    }

    public function execute(Request $request, CampBuilding $building): BuildingViewModel
    {
        $playerTech = $this->playerTechRepository->findByPlayer($request->attributes->get('player'));

        $viewModel = new ResearchCenterViewModel($building);
        $viewModel->playerTech = ($playerTech ?? new PlayerTech());

        $techs = [];
        foreach ($this->registry->getAll() as $techDef) {
            $techs[$techDef->getName()] = new ResearchTech($techDef);
        }

        $viewModel->techs = $techs;

        return $viewModel;
    }

    public static function getName(): string
    {
        return 'list_techs';
    }
}
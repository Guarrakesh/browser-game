<?php

namespace App\Modules\Research\Controller\ResearchCenter\Action;

use App\Constants;
use App\Entity\World\PlayerTech;
use App\Modules\Construction\Controller\AbstractBuildingAction;
use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Core\Entity\Planet;
use App\Modules\Core\ViewModel\BuildingViewModel;
use App\Modules\Research\DTO\ResearchTech;
use App\Modules\Research\ViewModel\ResearchCenterViewModel;
use App\ObjectRegistry\BuildingRegistry;
use App\ObjectRegistry\ResearchTechRegistry;
use App\Repository\PlayerTechRepository;
use App\Service\ResearchService;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[AsBuildingAction(Constants::RESEARCH_CENTER)]
#[Route('/research_center', name: 'research_center_index', methods: ['GET'])]
class IndexAction extends AbstractBuildingAction
{

    public function __construct(ContainerInterface                    $container,
                                private readonly ResearchTechRegistry $registry,
                                private readonly PlayerTechRepository $playerTechRepository,
                                private readonly BuildingRegistry $buildingRegistry,
                                private readonly ResearchService $researchService,
    )
    {
        parent::__construct($container);
    }

    public function __invoke(Request $request, Planet $planet): BuildingViewModel
    {
        $playerTech = $this->playerTechRepository->findByPlayer($planet->getPlayer());
        $building = $planet->getBuilding(Constants::RESEARCH_CENTER);
        $viewModel = new ResearchCenterViewModel($building);
        $viewModel->playerTech = ($playerTech ?? new PlayerTech());

        $techs = [];
        foreach ($this->registry->getAll() as $techDef) {
            $tech = new ResearchTech($techDef);
            $techs[$techDef->getName()] = $tech;
            $tech->satisfied = $this->researchService->canBeResearched($planet, $techDef->getName());


        }

        $viewModel->researchQueue = $this->researchService->getResearchQueue($planet->getPlayer(), $planet);
        $viewModel->techs = $techs;
        $viewModel->template = 'planet/buildings/research_center/index.html.twig';
        return $viewModel;
    }



    public static function getName(): string
    {
        return ActionEnum::IndexAction->value;
    }
}
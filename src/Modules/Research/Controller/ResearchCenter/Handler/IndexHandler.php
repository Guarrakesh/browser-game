<?php

namespace App\Modules\Research\Controller\ResearchCenter\Handler;

use App\Entity\World\PlayerTech;
use App\Modules\Construction\Controller\AbstractBuildingAction;
use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Planet\Dto\PlanetDTO;
use App\Modules\Planet\Infra\Registry\ResearchTechRegistry;
use App\Modules\Planet\ViewModel\BuildingViewModel;
use App\Modules\Research\DTO\ResearchTechDTO;
use App\Modules\Research\ViewModel\ResearchCenterViewModel;
use App\Modules\Shared\Constants;
use App\Service\ResearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[AsBuildingAction(Constants::RESEARCH_CENTER)]
#[Route('/research_center', name: 'research_center_index', methods: ['GET'])]
class IndexHandler extends AbstractBuildingAction
{

    public function __construct(
                                private readonly ResearchTechRegistry $registry,

                                private readonly ResearchService $researchService,
    )
    {

    }

    public function __invoke(Request $request, PlanetDTO $planet): BuildingViewModel
    {
        //$playerTech = $this->playerTechRepository->findByPlayer($planet->getPlayer());

        //$building = $planet->buildings[Constants::RESEARCH_CENTER];

        $viewModel = new ResearchCenterViewModel($planet->buildings[Constants::RESEARCH_CENTER], $planet);


        $queue = $this->researchService->getResearchQueue($planet->playerId, $planet);

        $viewModel->researches = $queue->jobs;
        $viewModel->playerTech = ($playerTech ?? new PlayerTech());

        $techs = [];
        foreach ($this->registry->getAll() as $techDef) {
            $tech = new ResearchTechDTO($techDef);
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
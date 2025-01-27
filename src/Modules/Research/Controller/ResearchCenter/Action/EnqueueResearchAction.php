<?php

namespace App\Modules\Research\Controller\ResearchCenter\Action;

use App\Constants;
use App\Modules\Construction\Controller\AbstractBuildingAction;
use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Core\Entity\PlanetBuilding;
use App\Modules\Core\ViewModel\BuildingViewModel;
use App\Modules\Research\ViewModel\ResearchCenterViewModel;
use App\ObjectRegistry\ResearchTechRegistry;
use App\Service\ResearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[AsBuildingAction(Constants::RESEARCH_CENTER)]
#[Route('/research_center/enqueue', name: 'research_center_enqueue', methods: ['POST'])]
class EnqueueResearchAction extends AbstractBuildingAction
{

    public function __invoke(
        PlanetBuilding $building, Request $request, ResearchService $researchService, ResearchTechRegistry $researchTechRegistry): BuildingViewModel
    {


        $payload = $request->getPayload();
        $techName = $payload->get('tech_name');

        $viewModel = new ResearchCenterViewModel($building);
        if ($techName && $research = $researchTechRegistry->find($techName)) {
            $researchService->enqueueResearch($building->getPlanet(), $techName);
            $viewModel->addMessage('success', 'Research Enqueued.');
        }

        return $viewModel;
    }

    public static function getName(): string
    {
        return ActionEnum::EnqueueResearch->value;
    }
}
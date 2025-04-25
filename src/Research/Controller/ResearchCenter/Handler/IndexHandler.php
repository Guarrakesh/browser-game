<?php

namespace App\Research\Controller\ResearchCenter\Handler;

use App\Modules\Construction\Controller\AbstractBuildingAction;
use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Research\Service\ResearchService;
use App\Research\ViewModel\ResearchCenterViewModel;
use App\Shared\Constants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[AsBuildingAction(Constants::RESEARCH_CENTER)]
#[Route('/research_center', name: 'research_center_index', methods: ['GET'])]
class IndexHandler extends AbstractBuildingAction
{

    public function __construct(
        private readonly ResearchService $researchService,
    )
    {

    }

    public function __invoke(Request $request, int $planetId): ResearchCenterViewModel
    {
        $researchCenter = $this->researchService->getResearchCenterOverview($planetId);

        return new ResearchCenterViewModel($researchCenter, null, null, 'planet/buildings/research_center/index.html.twig');
    }


    public static function getName(): string
    {
        return ActionEnum::IndexAction->value;
    }
}
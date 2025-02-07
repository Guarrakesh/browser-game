<?php

namespace App\Modules\Planet\Infra\Controller\ControlHub;

use App\Modules\Construction\Controller\AbstractBuildingAction;
use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Planet\Service\ControlHubService;
use App\Modules\Planet\Service\PlanetOverviewService;
use App\Modules\Planet\ViewModel\ControlHubViewModel;
use App\Modules\Shared\Constants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[AsBuildingAction(Constants::CONTROL_HUB)]
#[Route('/control_hub', 'control_hub_index')]
class IndexHandler extends AbstractBuildingAction
{
    public function __construct(
        private readonly ControlHubService $controlHubService,
        private readonly PlanetOverviewService $planetOverviewService,
    )
    {
    }

    public function __invoke(int $planetId, Request $request): ControlHubViewModel
    {

        $controlHub = $this->controlHubService->getControlHubOverview($planetId);
        $planet = $this->planetOverviewService->getPlanetOverview($planetId);

        return new ControlHubViewModel($controlHub, null, null, 'planet/buildings/control_hub/index.html.twig');
    }

    public static function getName(): string
    {
        return ActionEnum::IndexAction->value;
    }
}
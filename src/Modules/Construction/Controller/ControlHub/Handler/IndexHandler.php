<?php

namespace App\Modules\Construction\Controller\ControlHub\Handler;

use App\Constants;
use App\Modules\Construction\Controller\AbstractBuildingAction;
use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Construction\DTO\PossibleConstructionsDTO;
use App\Modules\Construction\Messenger\ConstructionMessenger;
use App\Modules\Construction\Service\ConstructionService;
use App\Modules\Core\BuildingMessenger;
use App\Modules\Core\DTO\PlanetDTO;
use App\Modules\Core\ViewModel\BuildingViewModel;
use App\Modules\Core\ViewModel\ControlHubViewModel;
use App\ObjectRegistry\BuildingRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[AsBuildingAction(Constants::CONTROL_HUB)]
#[Route('/control_hub', 'control_hub_index')]
class IndexHandler extends AbstractBuildingAction
{
    public function __construct(
        ContainerInterface                     $container,
        private readonly ConstructionMessenger $constructionMessenger, private readonly BuildingMessenger $buildingMessenger)
    {
        parent::__construct($container);
    }

    public function __invoke(PlanetDTO $planet, Request $request): BuildingViewModel
    {

        $building = $planet->buildings[Constants::CONTROL_HUB];
        $viewModel = new ControlHubViewModel($building, $planet, null, 'planet/buildings/control_hub/index.html.twig');
        $viewModel->constructionQueue = $this->constructionMessenger->sendGetConstructionQueueRequest($planet);
        $viewModel->buildings = $this->buildingMessenger->sendGetAllBuildingsRequest();
        $viewModel->possibleConstructions = $this->constructionMessenger->sendGetPossibleConstructionsRequest($planet);

        return $viewModel;
    }

    public static function getName(): string
    {
        return ActionEnum::IndexAction->value;
    }
}
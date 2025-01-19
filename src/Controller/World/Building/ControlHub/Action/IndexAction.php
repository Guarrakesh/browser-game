<?php

namespace App\Controller\World\Building\ControlHub\Action;

use App\Constants;
use App\Controller\World\Building\AbstractBuildingAction;
use App\Controller\World\Building\AsBuildingAction;
use App\Entity\World\CampBuilding;
use App\Model\ViewModel\BaseViewModel;
use App\Model\ViewModel\BuildingViewModel;
use App\Model\ViewModel\ControlHubViewModel;
use App\ObjectRegistry\BuildingRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;

#[AsBuildingAction(Constants::CONTROL_HUB)]
class IndexAction extends AbstractBuildingAction
{
    public function __construct(ContainerInterface $container, private readonly BuildingRegistry $buildingRegistry)
    {
        parent::__construct($container);
    }

    public function execute(Request $request, CampBuilding $building): BuildingViewModel
    {
        $buildings = $this->buildingRegistry->getAllConfigs();

        $viewModel = new ControlHubViewModel($building, null,'camp/buildings/control_hub/index.html.twig');
        $viewModel->buildings = $buildings;

        return $viewModel;
    }

    public static function getName(): string
    {
        return 'index_action';
    }
}
<?php

namespace App\Modules\Construction\Controller\ControlHub\Handler;

use App\Constants;
use App\Modules\Construction\Controller\AbstractBuildingAction;
use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Construction\Service\ConstructionService;
use App\Modules\Core\Entity\PlanetBuilding;
use App\Modules\Core\ViewModel\BuildingViewModel;
use App\Modules\Core\ViewModel\ControlHubViewModel;
use App\Repository\PlanetConstructionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[AsBuildingAction(Constants::CONTROL_HUB)]
#[Route('/control_hub/cancel_construction', 'control_hub_cancel_construction', methods: ['POST'])]
class CancelConstructionAction extends AbstractBuildingAction
{
    public function __construct(
        private readonly ConstructionService   $constructionService,
        private readonly UrlGeneratorInterface $generator,
        private readonly PlanetConstructionRepository $planetConstructionRepository
    )
    {
    }


    public function __invoke(PlanetBuilding $building, Request $request): BuildingViewModel
    {
        $constructionId = $request->getPayload()->get('construction_id');
        $construction = $this->planetConstructionRepository->find($constructionId);
        $viewModel = new ControlHubViewModel($building);
        if ($construction) {
            $this->constructionService->cancelConstruction($construction);
            $viewModel->addMessage('success', 'ConstructionDTO has been canceled');
        }

       // $viewModel->response = new RedirectResponse($this->generator->generate('planet_building', ['name' => Constants::CONTROL_HUB]));

        return $viewModel;
    }

    public static function getName(): string
    {
        return ActionEnum::CancelConstruction->value;
    }

}
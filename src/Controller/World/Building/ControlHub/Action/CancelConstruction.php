<?php

namespace App\Controller\World\Building\ControlHub\Action;

use App\Constants;
use App\Controller\World\Building\AsBuildingAction;
use App\Controller\World\Building\BuildingActionInterface;
use App\Entity\World\CampBuilding;
use App\Model\ViewModel\BuildingViewModel;
use App\Repository\CampConstructionRepository;
use App\Service\ConstructionService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsBuildingAction(Constants::CONTROL_HUB)]
readonly class CancelConstruction implements BuildingActionInterface
{
    public function __construct(
        private ConstructionService        $constructionService,
        private UrlGeneratorInterface $generator,
        private CampConstructionRepository $campConstructionRepository
    )
    {
    }

    public function execute(Request $request, CampBuilding $building): BuildingViewModel
    {
        $construction = $this->campConstructionRepository->find($request->get('payload'));
        if ($construction) {
            $this->constructionService->cancelConstruction($construction);
        }

        $viewModel = new BuildingViewModel($building);
        $viewModel->response = new RedirectResponse($this->generator->generate('camp_building', ['name' => Constants::CONTROL_HUB]));

        return $viewModel;
    }

    public static function getName(): string
    {
        return 'cancel_construction';
    }

}
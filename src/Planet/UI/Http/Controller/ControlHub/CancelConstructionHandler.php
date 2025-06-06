<?php

namespace App\Planet\UI\Http\Controller\ControlHub;

use App\Modules\Construction\Controller\AbstractBuildingAction;
use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Planet\Service\ControlHubService;
use App\Planet\UI\Http\Request\CancelConstructionRequestDTO;
use App\Planet\ViewModel\ControlHubViewModel;
use App\Shared\Constants;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[AsBuildingAction(Constants::CONTROL_HUB)]
#[Route('/control_hub/cancel_construction', 'control_hub_cancel_construction', methods: ['POST'])]
class CancelConstructionHandler extends AbstractBuildingAction
{
    public function __construct(
        private readonly ControlHubService $controlHubService,
        private readonly UrlGeneratorInterface $urlGenerator,
    )
    {
    }


    public function __invoke(
        #[MapRequestPayload] CancelConstructionRequestDTO $cancelConstructionRequest,
        int                                               $planetId,
    ): ControlHubViewModel
    {
        if ($cancelConstructionRequest->planetId && $planetId !== $cancelConstructionRequest->planetId) {
            throw new BadRequestHttpException("Invalid request.");
        }
        $controlHub = $this->controlHubService->cancelConstruction($planetId, $cancelConstructionRequest->constructionId);
        $viewModel = new ControlHubViewModel($controlHub);
        $viewModel->response = new RedirectResponse($this->urlGenerator->generate('control_hub_index'));
        $viewModel->addMessage('warning', "The construction order has been canceled");


        return $viewModel;
    }

    public static function getName(): string
    {
        return ActionEnum::CancelConstruction->value;
    }

}
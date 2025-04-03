<?php

namespace App\Modules\Planet\Controller\ControlHub;

use App\Modules\Construction\Controller\AbstractBuildingAction;
use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Planet\Dto\CancelConstructionRequestDTO;
use App\Modules\Planet\Service\ControlHubService;
use App\Modules\Planet\ViewModel\ControlHubViewModel;
use App\Modules\Shared\Constants;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[AsBuildingAction(Constants::CONTROL_HUB)]
#[IsGranted('ROLE_CAN_TERMINATE_CONSTRUCTIONS')]
#[Route('/control_hub/terminate_construction', 'control_hub_terminate_construction', methods: ['POST'])]
class TerminateConstructionHandler extends AbstractBuildingAction
{
    public function __construct(
        private readonly ControlHubService $controlHubService,
        private readonly UrlGeneratorInterface $urlGenerator,
    )
    {
    }


    public function __invoke(
        #[MapRequestPayload] CancelConstructionRequestDTO $terminateConstructionRequest,
        int                                               $planetId,
    ): ControlHubViewModel
    {
        if ($terminateConstructionRequest->planetId && $planetId !== $terminateConstructionRequest->planetId) {
            throw new BadRequestHttpException("Invalid request.");
        }
        $controlHub = $this->controlHubService->terminateConstruction($planetId, $terminateConstructionRequest->constructionId);
        $viewModel = new ControlHubViewModel($controlHub);
        $viewModel->response = new RedirectResponse($this->urlGenerator->generate('control_hub_index'));
        $viewModel->addMessage('success', "The order has been terminated.");


        return $viewModel;
    }

    public static function getName(): string
    {
        return ActionEnum::CancelConstruction->value;
    }

}
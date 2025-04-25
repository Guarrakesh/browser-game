<?php

namespace App\Planet\UI\Http\Controller\ControlHub;

use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Construction\Controller\BuildingActionInterface;
use App\Planet\Service\ControlHubService;
use App\Planet\UI\Http\Request\EnqueueConstructionRequestDTO;
use App\Planet\ViewModel\ControlHubViewModel;
use App\Shared\Constants;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[AsBuildingAction(Constants::CONTROL_HUB)]
#[Route('/control_hub/enqueue_construction', name: 'control_hub_enqueue_construction', methods: ['POST'])]
readonly class EnqueueConstructionHandler implements BuildingActionInterface
{

    public function __construct(private UrlGeneratorInterface $urlGenerator,
                                private ControlHubService     $controlHubService
    )
    {
    }

    public function __invoke(
        #[MapRequestPayload()] EnqueueConstructionRequestDTO $enqueueConstructionRequest,
        Request                                              $request,
        int                                                  $planetId,
    ): ControlHubViewModel
    {
        if ($enqueueConstructionRequest->planetId && $planetId !== $enqueueConstructionRequest->planetId) {
            throw new BadRequestHttpException("Invalid request.");
        }
        $controlHub = $this->controlHubService->enqueueConstruction($planetId, $enqueueConstructionRequest->building);
        $viewModel = new ControlHubViewModel($controlHub);
        $viewModel->response = new RedirectResponse($this->urlGenerator->generate('control_hub_index'));
        $viewModel->addMessage('success', "The building has been enqueued");
        return $viewModel;

    }

    public static function getName(): string
    {
        return ActionEnum::EnqueueConstruction->value;
    }
}
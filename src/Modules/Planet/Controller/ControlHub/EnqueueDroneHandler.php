<?php

namespace App\Modules\Planet\Controller\ControlHub;

use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Construction\Controller\BuildingActionInterface;
use App\Modules\Core\ViewModel\BaseViewModel;
use App\Modules\Planet\Dto\EnqueueDroneRequestDTO;
use App\Modules\Planet\Service\ControlHubService;
use App\Modules\Planet\Service\DroneService;
use App\Modules\Planet\ViewModel\ControlHubViewModel;
use App\Modules\Shared\Constants;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsBuildingAction(Constants::CONTROL_HUB)]
#[Route('/control_hub/enqueue_drone', name: 'control_hub_enqueue_drone', methods: ['POST'])]
readonly class EnqueueDroneHandler implements BuildingActionInterface
{

    public function __construct(private ControlHubService $controlHubService, private UrlGeneratorInterface $urlGenerator, private TranslatorInterface $translator, private DroneService $droneService)
    {
    }

    public function __invoke(
        #[MapRequestPayload] EnqueueDroneRequestDTO $enqueueDroneRequest,
        Request                                     $request,
        int $planetId
    ): BaseViewModel
    {
        if ($planetId !== $enqueueDroneRequest->planetId) {
            throw new BadRequestHttpException('Invalid request.');
        }

        $this->droneService->enqueueDrone($enqueueDroneRequest->planetId);


        $viewModel = new BaseViewModel();
        $viewModel->addMessage('success', $this->translator->trans('drone.enqueued',[], 'planet'));
        $viewModel->response = new RedirectResponse($this->urlGenerator->generate('control_hub_index'));

        return $viewModel;
    }


    public static function getName(): string
    {
        return ActionEnum::EnqueueDrone->value;
    }
}
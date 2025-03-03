<?php

namespace App\Modules\Research\Controller\ResearchCenter\Handler;

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


#[AsBuildingAction(Constants::CONTROL_HUB)]
#[Route('//research_center/cancel_research', 'research_center_cancel_research', methods: ['POST'])]
class CancelResearchHandler extends AbstractBuildingAction
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
        if ($planetId !== $cancelConstructionRequest->planetId) {
            throw new BadRequestHttpException("Invalid request.");
        }
        $controlHub = $this->controlHubService->cancelConstruction($planetId, $cancelConstructionRequest->constructionId);
        $viewModel = new ControlHubViewModel($controlHub);
        $viewModel->response = new RedirectResponse($this->urlGenerator->generate('control_hub_index'));
        $viewModel->addMessage('success', "The order has been canceled");


        return $viewModel;
    }

    public static function getName(): string
    {
        return ActionEnum::CancelConstruction->value;
    }

}
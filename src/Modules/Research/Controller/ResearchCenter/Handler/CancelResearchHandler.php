<?php

namespace App\Modules\Research\Controller\ResearchCenter\Handler;

use App\Modules\Construction\Controller\AbstractBuildingAction;
use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Planet\Dto\CancelConstructionRequestDTO;
use App\Modules\Planet\Service\ControlHubService;
use App\Modules\Planet\ViewModel\ControlHubViewModel;
use App\Modules\Research\Dto\CancelResearchRequestDTO;
use App\Modules\Research\Service\ResearchService;
use App\Modules\Research\ViewModel\ResearchCenterViewModel;
use App\Modules\Shared\Constants;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[AsBuildingAction(Constants::RESEARCH_CENTER)]
#[Route('//research_center/cancel_research', 'research_center_cancel_research', methods: ['POST'])]
class CancelResearchHandler extends AbstractBuildingAction
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ResearchService $researchService,
    )
    {
    }


    public function __invoke(
        #[MapRequestPayload] CancelResearchRequestDTO $cancelConstructionRequest,
        int                                           $planetId,
    ): ResearchCenterViewModel
    {

        $researchCenter = $this->researchService->cancelResearch($planetId, $cancelConstructionRequest->researchId);

        $viewModel = new ResearchCenterViewModel($researchCenter);
        $viewModel->response = new RedirectResponse($this->urlGenerator->generate('research_center_index'));
        $viewModel->addMessage('success', 'The Research order has been canceled.');


        return $viewModel;
    }

    public static function getName(): string
    {
        return ActionEnum::CancelResearch->value;
    }

}
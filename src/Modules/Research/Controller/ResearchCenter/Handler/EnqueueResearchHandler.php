<?php

namespace App\Modules\Research\Controller\ResearchCenter\Handler;

use App\Modules\Construction\Controller\AbstractBuildingAction;
use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Planet\Model\Entity\PlanetBuilding;
use App\Modules\Planet\ViewModel\BuildingViewModel;
use App\Modules\Research\Dto\EnqueueResearchRequestDTO;
use App\Modules\Research\Infra\Registry\ResearchTechRegistry;
use App\Modules\Research\Service\ResearchService;
use App\Modules\Research\ViewModel\ResearchCenterViewModel;
use App\Modules\Shared\Constants;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsBuildingAction(Constants::RESEARCH_CENTER)]
#[Route('/research_center/enqueue', name: 'research_center_enqueue', methods: ['POST'])]
class EnqueueResearchHandler extends AbstractBuildingAction
{

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function __invoke(
        #[MapRequestPayload()] EnqueueResearchRequestDTO $enqueueResearchRequest,
        int                       $planetId,
        Request                   $request,
        ResearchService           $researchService
    ): ResearchCenterViewModel
    {

        if ($planetId !== $enqueueResearchRequest->planetId) {
            throw new BadRequestHttpException("Invalid request.");
        }

        $researchCenter = $researchService->enqueueResearch($enqueueResearchRequest->planetId, $enqueueResearchRequest->techName);

        $viewModel = new ResearchCenterViewModel($researchCenter);
        $viewModel->response = new RedirectResponse($this->urlGenerator->generate('research_center_index'));
        $viewModel->addMessage('success', 'The Research has been enqueued.');

        return $viewModel;
    }

    public static function getName(): string
    {
        return ActionEnum::EnqueueResearch->value;
    }
}
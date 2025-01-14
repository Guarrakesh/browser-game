<?php

namespace App\Controller\World\Building\ControlHub\Action;

use App\Constants;
use App\Construction\ConstructionService;
use App\Controller\World\Building\BuildingActionInterface;
use App\Entity\World\CampBuilding;
use App\Repository\CampConstructionRepository;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsTaggedItem('cancel_construction')]
#[AutoconfigureTag(Constants::CONTROL_HUB . '.actions')]
readonly class CancelConstruction implements BuildingActionInterface
{
    public function __construct(
        private ConstructionService        $constructionService,
        private UrlGeneratorInterface $generator,
        private CampConstructionRepository $campConstructionRepository
    )
    {
    }

    public function execute(Request $request, CampBuilding $building): ?Response
    {
        $construction = $this->campConstructionRepository->find($request->get('payload'));
        if ($construction) {
            $this->constructionService->cancelConstruction($construction);
        }

        return new RedirectResponse($this->generator->generate('camp_building', ['name' => Constants::CONTROL_HUB]));

    }

}
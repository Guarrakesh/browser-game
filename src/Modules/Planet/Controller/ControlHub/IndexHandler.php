<?php

namespace App\Modules\Planet\Controller\ControlHub;

use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Planet\Service\ControlHubService;
use App\Modules\Planet\ViewModel\ControlHubViewModel;
use App\Modules\Shared\Constants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsBuildingAction(Constants::CONTROL_HUB)]
#[Route('/control_hub', 'control_hub_index')]
class IndexHandler extends AbstractController
{
    public function __construct(
        private readonly ControlHubService $controlHubService,
        private readonly TranslatorInterface $translator
    )
    {
    }

    public function __invoke(int $planetId, Request $request): ControlHubViewModel
    {

        $controlHub = $this->controlHubService->getControlHubOverview($planetId);

        return new ControlHubViewModel($controlHub, null, null, 'planet/buildings/control_hub/index.html.twig');
    }

    public static function getName(): string
    {
        return ActionEnum::IndexAction->value;
    }
}
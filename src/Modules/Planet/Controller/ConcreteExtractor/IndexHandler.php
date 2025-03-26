<?php

namespace App\Modules\Planet\Controller\ConcreteExtractor;

use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Core\ViewModel\BaseViewModel;
use App\Modules\Shared\Constants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[AsBuildingAction(Constants::CONCRETE_EXTRACTOR)]
#[Route('/concrete-extractor', 'concrete_extractor_index')]
class IndexHandler
{
    public function __invoke(int $planetId, Request $request): BaseViewModel
    {
        return new BaseViewModel(template: 'planet/buildings/concrete_extractor/index.html.twig');
    }
}
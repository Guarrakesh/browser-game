<?php

namespace App\Planet\ViewModel;

use App\Modules\Core\ViewModel\BaseViewModel;
use App\Planet\Dto\PlanetDTO;
use App\Shared\Dto\GameObjectWithRequirements;
use Symfony\Component\HttpFoundation\Response;

class BuildingViewModel extends BaseViewModel
{
    public function __construct(
        public readonly GameObjectWithRequirements $building,
        PlanetDTO                                  $planet,
        ?Response                                  $response = null,
        ?string                                    $template = null)
    {
        parent::__construct($planet, $response, $template);
    }

}
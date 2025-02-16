<?php

namespace App\Modules\Planet\ViewModel;

use App\Modules\Core\ViewModel\BaseViewModel;
use App\Modules\Planet\Dto\GameObjectWithRequirements;
use App\Modules\Planet\Dto\PlanetDTO;
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
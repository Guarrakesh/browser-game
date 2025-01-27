<?php

namespace App\Modules\Core\ViewModel;

use App\Modules\Core\DTO\PlanetBuildingDTO;
use App\Modules\Core\DTO\PlanetDTO;
use Symfony\Component\HttpFoundation\Response;

class BuildingViewModel extends BaseViewModel
{
    public function __construct(public PlanetBuildingDTO $building, PlanetDTO $planet, ?Response $response = null, ?string $template = null)
    {
        parent::__construct($planet, $response, $template);
    }

}
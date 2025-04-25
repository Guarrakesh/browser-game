<?php

namespace App\Planet\ViewModel;

use App\Modules\Core\ViewModel\BaseViewModel;
use App\Planet\Dto\ControlHubDTO;
use App\Planet\Dto\PlanetDTO;
use Symfony\Component\HttpFoundation\Response;

class ControlHubViewModel extends BaseViewModel
{

    public function __construct(
        public readonly ControlHubDTO $controlHub,
        ?PlanetDTO             $planet = null,
        ?Response                     $response = null,
        ?string                       $template = null
    )
    {
        parent::__construct($planet, $response, $template);
    }


}
<?php

namespace App\Modules\Planet\ViewModel;

use App\Modules\Core\ViewModel\BaseViewModel;
use App\Modules\Planet\Dto\PlanetDTO;
use Symfony\Component\HttpFoundation\Response;

class PlanetOverviewViewModel extends BaseViewModel
{
    public function __construct(public PlanetDTO $planetDTO, ?Response $response = null, ?string $template = null)
    {
        parent::__construct($response, $template);
    }
}
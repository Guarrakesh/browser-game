<?php

namespace App\Modules\Research\ViewModel;


use App\Modules\Core\ViewModel\BaseViewModel;
use App\Modules\Planet\Dto\PlanetDTO;
use App\Modules\Research\Dto\ResearchCenterDTO;
use Symfony\Component\HttpFoundation\Response;

class ResearchCenterViewModel extends BaseViewModel
{
    public function __construct(
        public readonly ResearchCenterDTO $researchCenter,
        ?PlanetDTO                        $planet = null,
        ?Response                         $response = null,
        ?string                           $template = null
    )
    {
        parent::__construct($planet, $response, $template);
    }


}
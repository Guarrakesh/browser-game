<?php

namespace App\Model\ViewModel;

use App\Entity\World\Camp;
use App\Entity\World\CampBuilding;
use Symfony\Component\HttpFoundation\Response;

class BuildingViewModel extends BaseViewModel
{
    public function __construct(public CampBuilding $building, ?Response $response = null, ?string $template = null)
    {
        parent::__construct($this->building->getCamp(), $response, $template);
    }

}
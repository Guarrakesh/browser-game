<?php

namespace App\Modules\Research\ViewModel;


use App\Modules\Core\ViewModel\BuildingViewModel;
use App\Modules\Research\DTO\ResearchTech;

class ResearchCenterViewModel extends BuildingViewModel
{
    /** @var array<string,ResearchTech> */
    public array $techs;


}
<?php

namespace App\Modules\Research\ViewModel;


use App\Modules\Planet\ViewModel\BuildingViewModel;
use App\Modules\Research\DTO\ResearchTechDTO;

class ResearchCenterViewModel extends BuildingViewModel
{
    /** @var array<string,ResearchTechDTO> */
    public array $techs;

    public array $researches;



}
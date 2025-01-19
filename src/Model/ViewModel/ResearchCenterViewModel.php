<?php

namespace App\Model\ViewModel;

use App\Entity\World\PlayerTech;
use App\Object\ResearchTech;

class ResearchCenterViewModel extends BuildingViewModel
{
    /** @var array<string,ResearchTech> */
    public array $techs;

    public PlayerTech $playerTech;

}
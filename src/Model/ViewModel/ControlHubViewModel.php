<?php

namespace App\Model\ViewModel;

use App\ObjectDefinition\Building\BuildingDefinitionInterface;

class ControlHubViewModel extends BuildingViewModel
{
    /** @var array<BuildingDefinitionInterface> */
    public array $buildings;

}
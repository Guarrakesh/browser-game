<?php

namespace App\Modules\Core\ViewModel;

use App\Modules\Construction\DTO\ConstructionQueueDTO;
use App\Modules\Construction\DTO\PossibleConstructionsDTO;
use App\Modules\Core\DTO\GameObjectDTO;

class ControlHubViewModel extends BuildingViewModel
{
    /** @var array<GameObjectDTO> */
    public array $buildings = [];

    public ?ConstructionQueueDTO $constructionQueue = null;

    public PossibleConstructionsDTO $possibleConstructions;

}
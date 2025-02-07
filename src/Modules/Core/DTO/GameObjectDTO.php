<?php

namespace App\Modules\Core\DTO;

use App\Modules\Planet\Dto\ObjectDefinition\Building\BuildingDefinition;
use App\Modules\Shared\Model\ObjectType;
use AutoMapper\Attribute\MapFrom;

class GameObjectDTO
{
    #[MapFrom(source: BuildingDefinition::class, property: 'name')]
    public ?string $name = null;

    #[MapFrom(source: BuildingDefinition::class, property: 'type')]
    public ?ObjectType $type = null;

}
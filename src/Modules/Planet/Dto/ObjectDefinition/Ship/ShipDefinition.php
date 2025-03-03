<?php

namespace App\Modules\Planet\Dto\ObjectDefinition\Ship;

use App\Modules\Planet\Dto\ObjectDefinition\AbstractDefinition;
use App\Modules\Shared\Model\ObjectType;

final class ShipDefinition extends AbstractDefinition implements ShipDefinitionInterface
{

    public function getType(): ObjectType
    {
        return ObjectType::Ship;
    }

    public function getRequirements(): array
    {
        return [];
    }
}
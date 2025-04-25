<?php

namespace App\Planet\GameObject\Ship;

use App\Shared\GameObject\AbstractDefinition;
use App\Shared\Model\ObjectType;

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
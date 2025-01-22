<?php

namespace App\ObjectDefinition\Ship;

use App\ObjectDefinition\AbstractDefinition;
use App\ObjectDefinition\ObjectType;

final class ShipDefinition extends AbstractDefinition implements ShipDefinitionInterface
{

    public function getType(): ObjectType
    {
        return ObjectType::Ship;
    }

}
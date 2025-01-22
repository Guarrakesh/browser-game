<?php

namespace App\ObjectDefinition\ShipComponent;

use App\ObjectDefinition\AbstractDefinition;
use App\ObjectDefinition\ObjectType;

final class ShipComponentDefinition extends AbstractDefinition implements ShipComponentDefinitionInterface
{

    public function getType(): ObjectType
    {
        return ObjectType::ShipComponent;
    }
}
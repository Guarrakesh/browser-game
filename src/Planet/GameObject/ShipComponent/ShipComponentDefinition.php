<?php

namespace App\Planet\GameObject\ShipComponent;

use App\Shared\GameObject\AbstractDefinition;
use App\Shared\Model\ObjectType;

final class ShipComponentDefinition extends AbstractDefinition implements ShipComponentDefinitionInterface
{

    public function getType(): ObjectType
    {
        return ObjectType::ShipComponent;
    }

    public function getRequirements(): array
    {
        return [];
    }
}
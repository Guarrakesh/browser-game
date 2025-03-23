<?php

namespace App\Modules\Planet\GameObject\ShipComponent;

use App\Modules\Shared\GameObject\AbstractDefinition;
use App\Modules\Shared\Model\ObjectType;

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
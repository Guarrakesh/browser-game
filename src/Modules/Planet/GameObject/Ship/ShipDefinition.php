<?php

namespace App\Modules\Planet\GameObject\Ship;

use App\Modules\Shared\GameObject\AbstractDefinition;
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
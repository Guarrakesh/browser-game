<?php

namespace App\Modules\Planet\Dto\ObjectDefinition\ShipComponent;

use App\Modules\Planet\Dto\ObjectDefinition\AbstractDefinition;
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
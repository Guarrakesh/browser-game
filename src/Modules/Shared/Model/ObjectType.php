<?php

namespace App\Modules\Shared\Model;

enum ObjectType
{
    case Building;
    case ResearchTech;
    case Unit;
    case Ship;
    case ShipComponent;

    public static function fromConfigLabel(string $label): ObjectType
    {
        return match($label) {
            'buildings' => self::Building,
            'techs' => self::ResearchTech,
            'ships' => self::Ship,
            'ship_components' => self::ShipComponent,
        };
    }
}
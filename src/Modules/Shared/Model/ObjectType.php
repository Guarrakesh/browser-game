<?php

namespace App\Modules\Shared\Model;

enum ObjectType: string
{
    case Building = 'building';
    case ResearchTech = 'research_tech';
    case Unit = 'unit';
    case Ship = 'ship';
    case ShipComponent = 'ship_component';
    case Drone = 'drone';

    public static function fromConfigLabel(string $label): ObjectType
    {
        return match($label) {
            'buildings' => self::Building,
            'techs' => self::ResearchTech,
            'ships' => self::Ship,
            'ship_components' => self::ShipComponent,
            'drones' => self::Drone
        };
    }
}
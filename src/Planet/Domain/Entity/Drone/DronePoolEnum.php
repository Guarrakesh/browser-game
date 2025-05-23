<?php

namespace App\Planet\Domain\Entity\Drone;

use App\Shared\Constants;

enum DronePoolEnum: string
{
    case STARTER_SHIP = 'starter_ship';
    case CONTROL_HUB = Constants::CONTROL_HUB;
    case CONCRETE_EXTRACTOR  = Constants::CONCRETE_EXTRACTOR;
    case METAL_REFINERY  = Constants::METAL_REFINERY;
    case HYDROPONIC_FARM = Constants::HYDROPONIC_FARM;
    case POLYMERS_FACTORY = Constants::POLYMER_FACTORY;

}

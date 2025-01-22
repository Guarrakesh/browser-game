<?php

namespace App\ObjectDefinition;

enum ObjectType
{
    case Building;
    case ResearchTech;
    case Unit;
    case Ship;
    case ShipComponent;
}
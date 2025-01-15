<?php

namespace App\Ship;

interface ShipDefinitionInterface
{
    public function getConfig(string $name): mixed;
}
<?php

namespace App\Energy\Domain;

use App\Planet\Domain\Entity\Planet;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface EnergyConsumerInterface
{
    public function getEnergyConsumption(Planet $planet): float;
}
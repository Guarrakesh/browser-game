<?php

namespace App\Service\Cost;

use App\Modules\Core\Entity\Planet;
use App\Object\ResourcePack;
use App\ObjectDefinition\BaseDefinitionInterface;

interface CostCalculatorInterface
{
    public function getCost(Planet $planet, BaseDefinitionInterface $definition, ?int $level): ResourcePack;
    public function supports(BaseDefinitionInterface $definition, ?int $level): bool;

}
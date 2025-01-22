<?php

namespace App\Service\Cost;

use App\Entity\World\Camp;
use App\Object\ResourcePack;
use App\ObjectDefinition\BaseDefinitionInterface;

interface CostCalculatorInterface
{
    public function getCost(Camp $camp, BaseDefinitionInterface $definition, ?int $level, $context = []): ResourcePack;
    public function supports(BaseDefinitionInterface $definition, ?int $level, array $context = []): bool;

}
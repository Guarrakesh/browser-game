<?php

namespace App\Service\Cost;

use App\Modules\Core\Entity\Planet;
use App\Object\ResourcePack;
use App\ObjectDefinition\BaseDefinitionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface CostEffectInterface
{
    public function processCost(Planet $planet, BaseDefinitionInterface $definition, int $level): ResourcePack;
}
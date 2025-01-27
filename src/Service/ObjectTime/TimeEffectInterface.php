<?php

namespace App\Service\ObjectTime;

use App\Modules\Core\Entity\Planet;
use App\ObjectDefinition\BaseDefinitionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface TimeEffectInterface
{
    public function processTime(Planet $planet, BaseDefinitionInterface $definition, int $level, int $time): int;
}
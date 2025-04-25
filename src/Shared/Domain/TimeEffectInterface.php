<?php

namespace App\Shared\Domain;

use App\Planet\Domain\Entity\Planet;
use App\Shared\GameObject\BaseDefinitionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface TimeEffectInterface
{
    public function processTime(Planet $planet, BaseDefinitionInterface $definition, int $level, int $time): int;
}
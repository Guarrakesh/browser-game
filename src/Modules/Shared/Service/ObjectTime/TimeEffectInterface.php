<?php

namespace App\Modules\Shared\Service\ObjectTime;

use App\Modules\Planet\Dto\ObjectDefinition\BaseDefinitionInterface;
use App\Modules\Planet\Model\Entity\Planet;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface TimeEffectInterface
{
    public function processTime(Planet $planet, BaseDefinitionInterface $definition, int $level, int $time): int;
}
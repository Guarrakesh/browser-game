<?php

namespace App\Modules\Shared\Service\ObjectTime;

use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Shared\GameObject\BaseDefinitionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface TimeEffectInterface
{
    public function processTime(Planet $planet, BaseDefinitionInterface $definition, int $level, int $time): int;
}
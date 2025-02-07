<?php

namespace App\Modules\Planet\Model\DomainService\Cost;

use App\Helper\MemoizerTrait;
use App\Modules\Planet\Dto\ObjectDefinition\BaseDefinitionInterface;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Shared\Model\ResourcePack;

class CostCalculator
{
    use MemoizerTrait;

    public function __construct()
    {
    }

    public function getCostForObject(Planet $planet, BaseDefinitionInterface $definition, ?int $level): ResourcePack
    {
        return $definition->getBaseCost()->multiply(
            round($definition->findParameter('cost_factor') ** ($level - 1))
        );

    }


}
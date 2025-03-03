<?php

namespace App\Modules\Shared\Service\Cost;

use App\Modules\Planet\Dto\MemoizerTrait;
use App\Modules\Planet\Dto\ObjectDefinition\BaseDefinitionInterface;
use App\Modules\Shared\Model\ResourcePack;

class CostCalculator
{
    use MemoizerTrait;

    public function __construct()
    {
    }

    public function getCostForObject(BaseDefinitionInterface $definition, ?int $level): ResourcePack
    {
        return $definition->getBaseCost()->multiply(
            $definition->findParameter('cost_factor') ** ($level - 1),
            true,
        );

    }


}
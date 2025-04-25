<?php

namespace App\Shared\Service\Cost;

use App\Planet\Dto\MemoizerTrait;
use App\Shared\GameObject\BaseDefinitionInterface;
use App\Shared\Model\ResourcePack;

class CostCalculator
{
    use MemoizerTrait;

    public function __construct()
    {
    }

    public function getCostForObject(BaseDefinitionInterface $definition, ?int $level): ResourcePack
    {

        $baseCost = $definition->getBaseCost();
        $factor = $definition->findParameter('cost_factor');
        if (is_numeric($level)) {
            return $baseCost->multiply($factor ** ($level - 1), true);
        }

        return $baseCost->multiply($factor, true);

    }


}
<?php

namespace App\Modules\Shared\Service\Cost;

use App\Modules\Planet\Dto\MemoizerTrait;
use App\Modules\Shared\GameObject\BaseDefinitionInterface;
use App\Modules\Shared\Model\ResourcePack;

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
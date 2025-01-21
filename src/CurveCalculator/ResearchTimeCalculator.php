<?php

namespace App\CurveCalculator;

use App\Object\ResourcePack;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('calculator_research_time')]
class ResearchTimeCalculator implements CurveCalculatorInterface
{

    public function calculateForLevel(int $level, ?float $base, array $parameters = []): float
    {

        $cost = $parameters['cost'];
        assert($cost instanceof ResourcePack);

        return
            ($cost->reduce(fn ($acc, $value) => $acc + $value))
            /
            (($base * $parameters['factor']) * (1 + $level-1));

    }

    public function validateParameters(array $parameters): bool
    {
        return ($parameters[1] ?? null) instanceof ResourcePack
             && is_numeric($parameters[0] ?? null);

    }
}
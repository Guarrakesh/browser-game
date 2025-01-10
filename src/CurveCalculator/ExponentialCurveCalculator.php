<?php

namespace App\CurveCalculator;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('calculator_exp')]
class ExponentialCurveCalculator implements CurveCalculatorInterface
{
    public function calculateForLevel(int $level, ?float $base, array $parameters = []): float
    {
        return ($base ?? 1.0) * ($parameters[0] ** $level);
    }

    public function validateParameters(array $parameters): bool
    {
       return isset($parameters[0]) && is_numeric($parameters[0]);
    }
}
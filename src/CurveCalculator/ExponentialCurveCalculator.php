<?php

namespace App\CurveCalculator;
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
<?php

namespace App\CurveCalculator;

interface CurveCalculatorInterface
{
    public function calculateForLevel(int $level, ?float $base, array $parameters = []): float;
    public function validateParameters(array $parameters): bool;
}
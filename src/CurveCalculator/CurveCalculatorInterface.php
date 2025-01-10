<?php

namespace App\CurveCalculator;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface CurveCalculatorInterface
{
    public function calculateForLevel(int $level, ?float $base, array $parameters = []): float;
    public function validateParameters(array $parameters): bool;
}
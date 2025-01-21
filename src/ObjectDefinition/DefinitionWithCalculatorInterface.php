<?php

namespace App\ObjectDefinition;

use App\CurveCalculator\CalculatorConfig;

interface DefinitionWithCalculatorInterface
{
    public function getCalculatorConfig(string $name): CalculatorConfig;
}
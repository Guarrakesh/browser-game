<?php

namespace App\CurveCalculator;

class CalculatorConfig
{
    public function __construct(public string $id, public array $parameters = [])
    {
    }
}
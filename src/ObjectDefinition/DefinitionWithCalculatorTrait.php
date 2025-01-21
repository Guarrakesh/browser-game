<?php

namespace App\ObjectDefinition;

use App\CurveCalculator\CalculatorConfig;
use LogicException;

trait DefinitionWithCalculatorTrait
{
    public function getCalculatorConfig(string $name): CalculatorConfig
    {

        if (isset($this->config[$name]['parameters']) && isset($this->config[$name]['service'])) {
            return new CalculatorConfig($this->config[$name]['service'], $this->config[$name]['parameters']);
        }
        if (isset($this->config['default_' . $name]['parameters']) && isset($this->config['default_' . $name]['service'])) {
            return new CalculatorConfig($this->config['default_' . $name]['service'], $this->config['default_' . $name]['parameters']);
        }

        throw new LogicException(sprintf("Expected to find %s config in building %s", $name, $this->getName()));
    }
}
<?php

namespace App\Modules\Planet\Model\Exception;

use App\Exception\GameException;
use App\Modules\Planet\Dto\ObjectDefinition\Building\BuildingDefinition;
use Throwable;

class InvalidBuildingConfigurationException extends GameException
{
    public function __construct(private readonly BuildingDefinition $buildingDefinition, string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct( sprintf("Invalid building configuration for %s: %s", $this->buildingDefinition->getName(), $message), $code, $previous);
    }

    public function getBuildingDefinition(): BuildingDefinition
    {
        return $this->buildingDefinition;
    }


}
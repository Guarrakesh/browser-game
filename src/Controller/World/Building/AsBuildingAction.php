<?php

namespace App\Controller\World\Building;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsBuildingAction extends AutoconfigureTag
{
    public function __construct(string $buildingName, array $attributes = [])
    {
        parent::__construct($buildingName . '.actions', $attributes);
    }
}
<?php

namespace App\Modules\Core;

use App\Modules\Core\DTO\GameObjectDTO;
use App\ObjectRegistry\BuildingRegistry;
use AutoMapper\AutoMapperInterface;

class BuildingMessenger
{
    public function __construct(private readonly BuildingRegistry $buildingRegistry, private readonly AutoMapperInterface $autoMapper)
    {
    }

    /**
     * @return array<GameObjectDTO>
     */
    public function sendGetAllBuildingsRequest(): array
    {
        $result = [];
        foreach ($this->buildingRegistry->getIterator() as $value)
        {
            $result[] = $this->autoMapper->map($value, new GameObjectDTO());
        }

        return $result;
    }
}
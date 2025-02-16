<?php

namespace App\Modules\Planet\Dto;

use App\Modules\Shared\Dto\GameObject;

class GameObjectWithRequirements
{
    /**
     * @param array<GameObjectLevel> $requirements
     */
    public function __construct(public GameObject $object, public  array $requirements = [])
    {
    }


}
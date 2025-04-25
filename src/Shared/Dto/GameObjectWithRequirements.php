<?php

namespace App\Shared\Dto;

readonly class GameObjectWithRequirements
{
    /**
     * @param array<GameObjectLevel> $requirements
     */
    public function __construct(public GameObject $object, public  array $requirements = [])
    {
    }


}
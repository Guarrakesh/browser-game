<?php

namespace App\Modules\Planet\Dto;

use App\Modules\Shared\Dto\GameObject;

readonly class GameObjectLevel
{

    public function __construct(private GameObject $object, private int $level)
    {
    }

    public function getObject(): GameObject
    {
        return $this->object;
    }


    public function getLevel(): int
    {
        return $this->level;
    }



}
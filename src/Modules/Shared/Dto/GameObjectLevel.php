<?php

namespace App\Modules\Shared\Dto;

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
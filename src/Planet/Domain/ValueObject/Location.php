<?php

namespace App\Planet\Domain\ValueObject;

use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class Location
{
    public function __construct(private int $galaxy, private int $system, private int $position)
    {
    }


}
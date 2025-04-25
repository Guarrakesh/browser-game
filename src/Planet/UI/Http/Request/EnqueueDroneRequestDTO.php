<?php

namespace App\Planet\UI\Http\Request;

use Symfony\Component\Validator\Constraints\NotBlank;

class EnqueueDroneRequestDTO
{

    #[NotBlank]
    public int $planetId;
}
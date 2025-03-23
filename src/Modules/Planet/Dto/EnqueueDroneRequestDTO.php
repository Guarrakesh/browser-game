<?php

namespace App\Modules\Planet\Dto;

use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
class EnqueueDroneRequestDTO
{

    #[NotBlank]
    public int $planetId;
}
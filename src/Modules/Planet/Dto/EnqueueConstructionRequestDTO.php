<?php

namespace App\Modules\Planet\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class EnqueueConstructionRequestDTO
{
    #[NotBlank]
    public string $building;

    #[NotBlank]
    public int $planetId;
}
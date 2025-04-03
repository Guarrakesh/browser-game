<?php

namespace App\Modules\Planet\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class EnqueueConstructionRequestDTO
{
    #[NotBlank]
    public string $building;

    public ?int $planetId = null;

}
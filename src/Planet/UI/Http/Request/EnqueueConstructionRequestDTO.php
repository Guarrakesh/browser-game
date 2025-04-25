<?php

namespace App\Planet\UI\Http\Request;

use Symfony\Component\Validator\Constraints\NotBlank;

class EnqueueConstructionRequestDTO
{
    #[NotBlank]
    public string $building;

    public ?int $planetId = null;

}
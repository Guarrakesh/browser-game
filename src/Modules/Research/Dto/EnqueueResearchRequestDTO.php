<?php

namespace App\Modules\Research\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class EnqueueResearchRequestDTO
{
    #[NotBlank]
    public string $techName;

    #[NotBlank]
    public int $planetId;
}
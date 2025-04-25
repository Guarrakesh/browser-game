<?php

namespace App\Research\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class EnqueueResearchRequestDTO
{
    #[NotBlank]
    public string $techName;

    #[NotBlank]
    public int $planetId;
}
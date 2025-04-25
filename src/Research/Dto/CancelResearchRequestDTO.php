<?php

namespace App\Research\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class CancelResearchRequestDTO
{
    #[NotBlank]
    public string $researchId;

    #[NotBlank]
    public int $planetId;
}
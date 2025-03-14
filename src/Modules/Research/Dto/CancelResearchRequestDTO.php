<?php

namespace App\Modules\Research\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class CancelResearchRequestDTO
{
    #[NotBlank]
    public string $researchId;

    #[NotBlank]
    public int $planetId;
}
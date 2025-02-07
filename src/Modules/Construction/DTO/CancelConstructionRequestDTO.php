<?php

namespace App\Modules\Construction\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;

class CancelConstructionRequestDTO
{
    #[NotBlank]
    public int $constructionId;

    #[NotBlank]
    public int $planetId;
}
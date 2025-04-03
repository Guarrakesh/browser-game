<?php

namespace App\Modules\Planet\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class CancelConstructionRequestDTO
{
    #[NotBlank]
    public int $constructionId;

    public ?int $planetId = null;
}
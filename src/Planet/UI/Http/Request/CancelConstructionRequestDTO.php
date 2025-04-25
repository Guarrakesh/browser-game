<?php

namespace App\Planet\UI\Http\Request;

use Symfony\Component\Validator\Constraints\NotBlank;

class CancelConstructionRequestDTO
{
    #[NotBlank]
    public int $constructionId;

    public ?int $planetId = null;
}
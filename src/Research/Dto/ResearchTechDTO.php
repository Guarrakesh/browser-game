<?php

namespace App\Research\Dto;

use App\Shared\Model\ResourcePack;

readonly class ResearchTechDTO
{
    public function __construct(
        public string $techName,
        public ResourcePack $cost,
        public int $researchTime,
        public bool $isCostSatisfied = false,
        public ?string $description = null
    )
    {
    }
}
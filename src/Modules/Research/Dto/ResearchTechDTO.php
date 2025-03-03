<?php

namespace App\Modules\Research\Dto;

use App\Modules\Research\Dto\ObjectDefinition\ResearchTechDefinition;
use App\Modules\Shared\Model\ResourcePack;
use Symfony\Component\DependencyInjection\Definition;

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
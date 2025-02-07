<?php

namespace App\Engine;

use App\Engine\Processor\ResearchProcessor;
use App\Modules\Planet\Service\ConstructionProcessor;

readonly class GameEngine
{
    public function __construct(
        private ConstructionProcessor $constructionProcessor,
        private ResearchProcessor $researchProcessor
    )
    {
    }

    public function run(): void
    {
        $timestamp = time();

        $this->constructionProcessor->process($timestamp);
        $this->researchProcessor->process($timestamp);
    }
}
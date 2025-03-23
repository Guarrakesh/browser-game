<?php

namespace App\Engine;

use App\Engine\Processor\ResearchProcessor;
use App\Modules\Planet\Service\ConstructionProcessor;
use App\Modules\Planet\Service\DroneQueueProcessor;

readonly class GameEngine
{
    public function __construct(
        private ConstructionProcessor $constructionProcessor,
        private ResearchProcessor     $researchProcessor, private DroneQueueProcessor $droneQueueProcessor
    )
    {
    }

    public function run(): void
    {
        $timestamp = time();

        $this->constructionProcessor->process($timestamp);
        $this->researchProcessor->process($timestamp);
        $this->droneQueueProcessor->process($timestamp);
    }
}
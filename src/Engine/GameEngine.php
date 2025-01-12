<?php

namespace App\Engine;

use App\Engine\Processor\ConstructionProcessor;
use App\Resource\ResourceService;

readonly class GameEngine
{
    public function __construct(
        private ConstructionProcessor $constructionProcessor,
    )
    {
    }

    public function run(): void
    {
        $timestamp = time();

        $this->constructionProcessor->process($timestamp);
    }
}
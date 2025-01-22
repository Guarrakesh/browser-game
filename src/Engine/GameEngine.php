<?php

namespace App\Engine;

use App\Engine\Processor\ConstructionProcessor;

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
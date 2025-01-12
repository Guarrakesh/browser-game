<?php

namespace App\Engine\Processor;

interface ProcessorInterface
{
    public function process(int $timestamp): void;
}
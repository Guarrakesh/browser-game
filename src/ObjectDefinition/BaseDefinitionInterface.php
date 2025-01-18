<?php

namespace App\ObjectDefinition;

interface BaseDefinitionInterface
{
    public function getConfig(string $name): mixed;
    public function getName(): string;

}
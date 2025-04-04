<?php

namespace App\Twig\Component;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Twig\Environment;

#[AsTwigComponent('Tabs')]
class Tabs
{
    public array $tabs = [];       // Array of ['key' => ['label' => '', 'content' => rendered HTML]]
    public ?string $active = null; // Key of the active tab

    public function getActiveTab(): ?string
    {
        return $this->active ?? array_key_first($this->tabs);
    }
}
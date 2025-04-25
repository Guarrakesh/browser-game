<?php

namespace App\Shared\Model\Entity;

use App\Shared\Model\Event\DomainEventInterface;

trait EventDispatcherTrait
{
    protected array $domainEvents;

    public function recordEvent(DomainEventInterface $event): static
    {
        $this->domainEvents[] = $event;

        return $this;
    }

    public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }



}
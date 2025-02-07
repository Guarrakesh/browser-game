<?php

namespace App\Modules\Planet\Model\Entity;

use DateInterval;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Gedmo\Mapping\Annotation\Timestampable;

#[MappedSuperclass]
class QueueJob
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column]
    protected ?DateTimeImmutable $completedAt = null;


    #[ORM\Column(nullable: true)]
    #[Timestampable]
    protected ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    protected ?DateTimeImmutable $cancelledAt = null;

    #[ORM\Column()]
    protected ?int $duration = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $processed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): QueueJob
    {
        $this->id = $id;
        return $this;
    }


    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?DateTimeImmutable $completedAt): QueueJob
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getStartedAt(): ?DateTimeImmutable
    {
        return $this->completedAt->sub(new DateInterval("PT{$this->duration}S"));
    }

    public function setStartedAt(?DateTimeImmutable $startedAt): QueueJob
    {
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): QueueJob
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCancelledAt(): ?DateTimeImmutable
    {
        return $this->cancelledAt;
    }

    public function setCancelledAt(?DateTimeImmutable $cancelledAt): QueueJob
    {
        $this->cancelledAt = $cancelledAt;
        return $this;
    }


    public function getRemainingTime(): DateInterval
    {
        $now = new DateTimeImmutable();

        return $now->diff($this->getCompletedAt());

    }

    /**
     * @return int The duration of the job, in seconds
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): QueueJob
    {
        $this->duration = $duration;

        return $this;
    }

    public function isProcessed(): bool
    {
        return $this->processed;
    }

    public function markAsProcessed(): static
    {
        $this->processed = true;

        return $this;
    }


}
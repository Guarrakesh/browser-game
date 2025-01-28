<?php

namespace App\Entity\World\Queue;

use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Gedmo\Mapping\Annotation\Timestampable;

use Doctrine\ORM\Mapping as ORM;

#[MappedSuperclass]
class QueueJob
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column]
    private ?DateTimeImmutable $completedAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    #[Timestampable]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $cancelledAt = null;

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
        return $this->startedAt;
    }

    public function setStartedAt(?DateTimeImmutable $startedAt): QueueJob
    {
        $this->startedAt = $startedAt;
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



}
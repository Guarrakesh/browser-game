<?php

namespace App\Entity\World;

use App\Entity\World\Queue\CampConstruction;
use App\Repository\ConstructionLogRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConstructionLogRepository::class)]
class ConstructionLog
{
    public const TYPE_COMPLETED = 'completed';
    public const TYPE_CANCELLED = 'cancelled';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    private ?string $buildingName = null;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getBuildingName(): ?string
    {
        return $this->buildingName;
    }

    public function setBuildingName(?string $buildingName): ConstructionLog
    {
        $this->buildingName = $buildingName;
        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): ConstructionLog
    {
        $this->level = $level;
        return $this;
    }


    public static function fromCompleted(CampConstruction $construction): static
    {
        $log = new ConstructionLog();
        $log->setBuildingName($construction->getBuildingName());
        $log->setLevel($construction->getLevel());
        $log->setType(self::TYPE_COMPLETED);
        $log->setCreatedAt($construction->getCompletedAt());

        return $log;
    }


    public static function fromCancelled(CampConstruction $construction): static
    {
        $log = new ConstructionLog();
        $log->setBuildingName($construction->getBuildingName());
        $log->setLevel($construction->getLevel());
        $log->setType(self::TYPE_CANCELLED);
        $log->setCreatedAt(new DateTimeImmutable());

        return $log;
    }
}

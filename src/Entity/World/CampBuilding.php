<?php

namespace App\Entity\World;

use App\Repository\CampBuildingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;

#[ORM\Entity(repositoryClass: CampBuildingRepository::class)]
#[ORM\UniqueConstraint(name: 'camp_building_unique', columns: ['camp_id', 'name'])]
class CampBuilding
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $level = null;

    #[ORM\Column(nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Timestampable]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'campBuildings')]
    #[ORM\JoinColumn(name: 'camp_id', nullable: false)]
    private ?Camp $camp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCamp(): ?Camp
    {
        return $this->camp;
    }

    public function setCamp(?Camp $camp): static
    {
        $this->camp = $camp;

        return $this;
    }
}

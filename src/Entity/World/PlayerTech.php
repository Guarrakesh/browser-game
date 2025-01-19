<?php

namespace App\Entity\World;

use App\Repository\PlayerTechRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerTechRepository::class)]
class PlayerTech
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $data = [];

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $player = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getLevel(string $name): int
    {
        return $this->data[$name] ?? 0;
    }
    public function hasLevel(string $name, int $level): bool
    {
        return isset($this->data[$name]) && $this->data[$name] >= $level;
    }


    public function incrementLevel(string $name): static
    {
        if (!$this->data[$name]) {
            $this->data[$name] = 0;
        }

        $this->data[$name] += 1;

        return $this;
    }

    public function decrementLevel(string $name): static
    {
        if ($this->data[$name]) {
            $this->data[$name] -= 1;
        }

        return $this;
    }

    public function hasTech(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player): static
    {
        $this->player = $player;

        return $this;
    }




}

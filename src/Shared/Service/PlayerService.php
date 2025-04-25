<?php

namespace App\Shared\Service;

use App\Exception\GameException;
use App\Shared\Dto\PlayerDTO;
use App\Shared\Repository\PlayerRepository;

class PlayerService
{
    public function __construct(private readonly PlayerRepository $playerRepository)
    {
    }

    public function getPlayer(int $playerId): PlayerDTO
    {
        $player = $this->playerRepository->find($playerId);
        if (!$player) {
            throw new GameException("Player not found.");
        }
        return new PlayerDTO($playerId, $player->getUserId(), $player->getJoinedAt());
    }
}
<?php

namespace App\Shared\Exception;

use App\Exception\GameException;
use App\Shared\Model\ResourcePack;
use Throwable;

class InsufficientResourcesException extends GameException
{
    public function __construct(private readonly ResourcePack $cost, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $message ??= "Insufficient Resources.";
        parent::__construct($message, $code, $previous);
    }

    public function getCost(): ResourcePack
    {
        return $this->cost;
    }


}
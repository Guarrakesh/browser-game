<?php

namespace App\Planet\Domain\Exception;

use App\Exception\GameException;
use App\Modules\Planet\Model\Exception\Throwable;
use App\Planet\Domain\Entity\Planet;

class FullQueueException extends GameException
{
    public function __construct(private readonly Planet $planet, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message ?: "Cannot enqueue any more ", $code, $previous);
    }

    public function getPlanet(): Planet
    {
        return $this->planet;
    }


}
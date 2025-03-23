<?php

namespace App\Modules\Planet\Model\Exception;

use App\Exception\GameException;
use App\Modules\Planet\Model\Entity\Planet;

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
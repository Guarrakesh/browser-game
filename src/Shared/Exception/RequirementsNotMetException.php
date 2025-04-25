<?php

namespace App\Shared\Exception;

use App\Exception\GameException;
use App\Shared\Dto\GameObject;
use Throwable;

class RequirementsNotMetException extends GameException
{
    public function __construct(private readonly array $requirements,  private readonly GameObject $object, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message ?? "Requirements not met.", $code, $previous);
    }

    public function getRequirements(): array
    {
        return $this->requirements;
    }

    public function getObject(): GameObject
    {
        return $this->object;
    }


}
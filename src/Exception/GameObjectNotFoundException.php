<?php

namespace App\Exception;

use App\ObjectDefinition\BaseDefinitionInterface;
use App\ObjectDefinition\ObjectType;

class GameObjectNotFoundException extends GameException
{
    public function __construct(private readonly ?ObjectType $type, private readonly ?string $name = null, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getType(): ?ObjectType
    {
        return $this->type;
    }

    public function getName(): ?string
    {
        return $this->name;
    }




}
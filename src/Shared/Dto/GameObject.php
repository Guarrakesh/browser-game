<?php

namespace App\Shared\Dto;

use App\Shared\Model\ObjectType;

class GameObject
{
    public function __construct(private string $name, private ObjectType $type, public readonly ?string $description = null) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ObjectType
    {
        return $this->type;
    }

    public function equalsTo(GameObject $object): bool
    {
        return $this->name === $object->name && $this->type === $object->type;
    }


}
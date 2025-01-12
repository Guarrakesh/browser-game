<?php

namespace App\Exception;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class PlayerNotFoundException extends GameException
{
    public function __construct(private readonly UserInterface $user, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = $message ?: sprintf("Player with User %s not found.", $user->getUserIdentifier());
        parent::__construct($message, $code, $previous);
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }


}
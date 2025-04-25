<?php

namespace App\Modules\Core\ViewModel;


use App\Planet\Dto\PlanetDTO;
use Symfony\Component\HttpFoundation\Response;

class BaseViewModel
{
    /**
     * @var array<string,array>
     */
    private array $messages = [];

    public function __construct(
        public ?PlanetDTO $planet = null,
        public ?Response $response = null,
        public ?string   $template = null)
    {
    }

    public function addMessage(string $type, string $message): self
    {
        if (!isset($this->messages[$type])) {
            $this->messages[$type] = [];
        }

        $this->messages[$type][] = $message;

        return $this;
    }


    public function hasMessages(): bool
    {
        return !empty($this->messages);
    }

    public function getMessages(): iterable
    {
        foreach ($this->messages as $type => $messages) {
            foreach ($messages as $message) {
                yield [$type, $message];
            }
        }
    }




}
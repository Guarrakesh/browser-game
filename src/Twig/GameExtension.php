<?php

namespace App\Twig;

use App\Entity\World\Player;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

#[AsTaggedItem('twig.extension')]
class GameExtension extends AbstractExtension
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('player', [$this, 'getPlayer']),
        ];
    }

    public function getPlayer(): Player
    {
        return $this->requestStack->getCurrentRequest()->attributes->get('player');
    }

}
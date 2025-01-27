<?php

namespace App\Twig;

use App\Entity\World\Player;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

#[AsTaggedItem('twig.extension')]
class GameExtension extends AbstractExtension
{
    public function __construct(private RequestStack $requestStack, private readonly UrlGeneratorInterface $router)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('player', [$this, 'getPlayer']),
            new TwigFunction('route_exists', [$this, 'routeExists']),
        ];
    }

    public function getPlayer(): Player
    {
        return $this->requestStack->getCurrentRequest()->attributes->get('player');
    }

    public function routeExists(string $route): bool
    {
        try {
            return $this->router->generate($route) !== null;
        } catch (RouteNotFoundException) {
            return false;
        }
    }

}
<?php

namespace App\Twig;

use App\Entity\World\Player;
use DateInterval;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

#[AsTaggedItem('twig.extension')]
class GameExtension extends AbstractExtension
{
    public function __construct(
        private RequestStack                   $requestStack,
        private readonly TranslatorInterface   $translator,
        private readonly UrlGeneratorInterface $router
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('player', [$this, 'getPlayer']),
            new TwigFunction('route_exists', [$this, 'routeExists']),
        ];
    }


    public function getFilters(): array
    {
        return [
            new TwigFilter('remaining_time', [$this, 'getRemainingTime']),
            new TwigFilter('format_build_time', [$this, 'formatBuildTime']),
        ];
    }

    public function getRemainingTime(DateInterval $remainingTime): string
    {
        $result = '';
        if ($remainingTime->days > 0) {
            $result = $remainingTime->format('%a')  . ' '
                . $this->translator->trans('days', ['days' => $remainingTime->days]) . ' ';
        }

        $result .= $remainingTime->format('%H:%I:%S');

        return $result;
    }

    public function formatBuildTime(int $seconds):string
    {
        $days = intdiv($seconds, 86400);
        $remainder = $seconds % 86400;
        $hours = intdiv($remainder, 3600);
        $minutes = intdiv($remainder % 3600, 60);
        $secs = $remainder % 60;

        $time = sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);

        if ($days > 0) {
            return sprintf('%d day%s %s', $days, $days > 1 ? 's' : '', $time);
        }

        return $time;
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
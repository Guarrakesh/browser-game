<?php

namespace App\Planet\Dto;

use Closure;

trait MemoizerTrait
{
    private static array $memoized;

    private function memoize(string $key, Closure $closure): mixed {
        if (!isset(static::$memoized[$key])) {
            static::$memoized[$key] = $closure();
        }

        return static::$memoized[$key];

    }
}
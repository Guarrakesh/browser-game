<?php

namespace App\Helper;

use Closure;

trait MemoizerTrait
{
    private static array $memoized;

    private function memoize(string $key, Closure $closure) {
        if (!isset(static::$memoized[$key])) {
            static::$memoized[$key] = $closure();
        }

        return static::$memoized[$key];

    }
}
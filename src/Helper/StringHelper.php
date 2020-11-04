<?php

namespace App\Helper;

class StringHelper
{
    /**
     * Determine if a given string contains a given substring.
     *
     * @param string          $haystack
     * @param string|string[] $needles
     *
     * @return bool
     */
    public static function contains(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' !== $needle && false !== mb_strpos($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}

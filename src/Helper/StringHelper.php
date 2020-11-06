<?php

namespace App\Helper;

class StringHelper
{
    /**
     * Determine if a given string contains a given substring.
     *
     * @param string|string[] $needles
     */
    public function contains(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' !== $needle && false !== mb_strpos($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}

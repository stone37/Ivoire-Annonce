<?php

namespace App\Util;

class UnsetArrayElementsUtil
{
    public function unsetElements(array $elements, array $keys): array
    {
        foreach ($keys as $key) {
            if (!isset($elements[$key])) {
                continue;
            }

            unset($elements[$key]);
        }

        return $elements;
    }
}
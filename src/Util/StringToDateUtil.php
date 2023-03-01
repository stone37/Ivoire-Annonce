<?php

namespace App\Util;

use DateTime;
use Exception;

class StringToDateUtil
{
    /**
     * @throws Exception
     */
    public function converter(string $date): DateTime
    {
        $asString = $date;

        if (ctype_digit($asString) || (!empty($asString) && '-' === $asString[0] && ctype_digit(substr($asString, 1)))) {
            $date = new DateTime('@'.$date);
        } else {
            $date = new DateTime($date);
        }

        return $date;
    }
}
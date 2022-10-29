<?php

namespace App\Helpers;

class DateHelper
{
    /**
     * Date validation
     *
     * @param string $date
     * @param string $format
     *
     * @return bool
     */
    public static function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }
}

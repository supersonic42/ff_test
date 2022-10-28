<?php

namespace App\Models\CurrencyInfoSrc;

use App\Models\CurrencyInfoSrc\Interfaces\RateInterface;

class CBR implements RateInterface
{
    public function getDateRangeRate(string $currIn, string $currOut, string $dateFrom, string $dateTo): array
    {
        return [
            $dateFrom => 2.34,
            $dateTo => 3.55,
        ];
    }

    public function getCurrencyCodeMap(): array
    {
        return [
            'USD' => 'R01235',
            'EUR' => 'R01239',
        ];
    }
}

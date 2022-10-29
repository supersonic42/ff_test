<?php

namespace App\Models\CurrencyInfoSrc\Interfaces;

interface RateInterface
{
    /**
     * Get currency rate in a range of dates
     *
     * @param string $currIn
     * @param string $currOut
     * @param string $dateFrom
     * @param string $dateTo
     *
     * @return bool|array
     */
    public function getDateRangeRate(string $currIn, string $currOut, string $dateFrom, string $dateTo): bool|array;

    /**
     * Mapping from ISO currency codes to particular codes in info source
     *
     * @return array
     */
    public function getCurrencyCodeMap(): array;
}
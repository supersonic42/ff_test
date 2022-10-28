<?php

namespace App\Models\CurrencyInfoSrc\Interfaces;

interface RateInterface
{
    public function getDateRangeRate(string $currIn, string $currOut, string $dateFrom, string $dateTo): bool|array;

    public function getCurrencyCodeMap(): array;
}
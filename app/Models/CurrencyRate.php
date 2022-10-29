<?php

namespace App\Models;

use App\Helpers\DateHelper;
use App\Models\CurrencyInfoSrc\Interfaces\RateInterface;

class CurrencyRate
{
    /**
     * @var string
     */
    public string $dateFrom;

    /**
     * @var string
     */
    public string $dateTo;

    /**
     * @var string
     */
    public string $currIn = 'RUR';

    /**
     * @var string
     */
    public string $currOut;

    /**
     * @var array
     */
    private array $_errors = [];

    /**
     * @var object
     */
    private object $_currencyInfoSrc;

    public function __construct(RateInterface $currencyInfoSrc)
    {
        $this->_currencyInfoSrc = $currencyInfoSrc;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        if (!DateHelper::validateDate($this->dateFrom)) {
            $this->_errors['dateFrom'] = 'Start date is not valid';
        }

        if (!DateHelper::validateDate($this->dateTo)) {
            $this->_errors['dateTo'] = 'End date is not valid';
        }

        $currencyCodeMap = $this->_currencyInfoSrc->getCurrencyCodeMap();

        if (!isset($currencyCodeMap[$this->currIn])) {
            $this->_errors['currIn'] = 'IN currency is not valid';
        }

        if (!isset($currencyCodeMap[$this->currOut])) {
            $this->_errors['currOut'] = 'OUT currency is not valid';
        }

        if ($this->currIn == $this->currOut) {
            $this->_errors['currInOut'] = 'IN and OUT currencies cannot be equal';
        }

        return empty($this->_errors);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->_errors;
    }

    /**
     * @return string
     */
    public function getCacheKey(): string
    {
        return "{$this->dateFrom}:{$this->dateTo}:{$this->currIn}:{$this->currOut}";
    }

    /**
     * Gets currency rate from external source
     *
     * @return array
     * @throws \Exception
     */
    public function getRate(): array
    {
        $rate = $this->_currencyInfoSrc->getDateRangeRate($this->currIn, $this->currOut, $this->dateFrom, $this->dateTo);

        if ($rate === false) {
            throw new \Exception('Error getting currency rate');
        }

        return $rate;
    }
}

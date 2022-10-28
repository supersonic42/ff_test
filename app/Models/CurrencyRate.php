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
            $this->_errors['dateFrom'] = 'Неверно указана дата начала диапазона';
        }

        if (!DateHelper::validateDate($this->dateTo)) {
            $this->_errors['dateTo'] = 'Неверно указана дата конца диапазона';
        }

        if (!isset($this->_currencyInfoSrc->getCurrencyCodeMap()[$this->currOut])) {
            $this->_errors['currOut'] = 'Неверно указана валюта конвертации';
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
     * Получение курса валют
     *
     * @return array
     * @throws \Exception
     */
    public function getRate(): array
    {
        $rate = $this->_currencyInfoSrc->getDateRangeRate($this->currIn, $this->currOut, $this->dateFrom, $this->dateTo);

        if ($rate === false) {
            throw new \Exception('Ошибка получения курса валют');
        }

        return $rate;
    }
}

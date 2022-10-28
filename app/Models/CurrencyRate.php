<?php

namespace App\Models;

use App\Helpers\DateHelper;

class CurrencyRate
{
    /**
     * @var string
     */
    public string $date;

    /**
     * @var string
     */
    public string $currIn;

    /**
     * @var string
     */
    public string $currOut;

    /**
     * @var array
     */
    private array $_errors = [];

    /**
     * @return bool
     */
    public function validate(): bool
    {
        if (!DateHelper::validateDate($this->date)) {
            $this->_errors['date'] = 'Неверно указана дата';
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
        return "{$this->date}:{$this->currIn}:{$this->currOut}";
    }

    /**
     * Получение курса валют
     *
     * @return float
     */
    public function getRate(): float
    {
        return 1.23;
    }
}

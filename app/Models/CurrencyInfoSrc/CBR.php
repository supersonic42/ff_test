<?php

namespace App\Models\CurrencyInfoSrc;

use App\Helpers\RequestHelper;
use App\Models\CurrencyInfoSrc\Interfaces\RateInterface;

class CBR implements RateInterface
{
    private string $baseUrl = 'http://www.cbr.ru/scripts/XML_dynamic.asp';

    /**
     * {@inheritDoc}
     */
    public function getDateRangeRate(string $currIn, string $currOut, string $dateFrom, string $dateTo): bool|array
    {
        $dateFromFormatted = (new \DateTime($dateFrom))->format('d/m/Y');
        $dateToFormatted = (new \DateTime($dateTo))->format('d/m/Y');

        $data = [];

        $data1 = $this->getDateRangeRateCommon($currOut, $dateFromFormatted, $dateToFormatted);

        if ($data1 === false) {
            return false;
        }

        /**
         * If IN currency is not RUR, then we do an additional ratio calculation
         */
        if ($currIn != 'RUR') {
            $data2 = $this->getDateRangeRateCommon($currIn, $dateFromFormatted, $dateToFormatted);

            if ($data2 === false) {
                return false;
            }

            /**
             * Exception for RUR, because CBR has no functionality to convert currencies to RUR
             */
            if ($currOut == 'RUR') {
                foreach ($data2 as $date => $rate) {
                    $data[$date] = round(1 / $rate, 4);
                }
            } else {
                foreach ($data1 as $date => $rate) {
                    if (isset($data2[$date])) {
                        $data[$date] = round($rate / $data2[$date], 4);
                    }
                }
            }
        } else {
            $data = $data1;
        }

        return $data;
    }

    public function getDateRangeRateCommon(string $curr, string $dateFrom, string $dateTo): bool|array
    {
        $res = RequestHelper::sendCurl($this->baseUrl, [
            'date_req1' => $dateTo,
            'date_req2' => $dateFrom,
            'VAL_NM_RQ' => $this->getCurrencyCodeMap()[$curr],
        ], 'get', [], [], false);

        if ($res['httpCode'] != 200) {
            return false;
        }

        $data = [];
        $document = new \SimpleXMLElement($res['result']);

        foreach ($document->Record as $v) {
            $key = date('Y-m-d', strtotime($v->attributes()['Date']->__toString()));
            $value = str_replace(',', '.', $v->Value->__toString());
            $valueForOneItem = round($value / (int) $v->Nominal->__toString(), 4);
            $data[$key] = $valueForOneItem;
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyCodeMap(): array
    {
        return [
            'RUR' => '',
            'USD' => 'R01235',
            'EUR' => 'R01239',
            'TRY' => 'R01700',
        ];
    }
}

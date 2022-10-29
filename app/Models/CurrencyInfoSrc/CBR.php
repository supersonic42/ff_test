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
        $dateFromObj = new \DateTime($dateFrom);
        $dateToObj = new \DateTime($dateTo);

        $res = RequestHelper::sendCurl($this->baseUrl, [
            'date_req1' => $dateToObj->format('d/m/Y'),
            'date_req2' => $dateFromObj->format('d/m/Y'),
            'VAL_NM_RQ' => $this->getCurrencyCodeMap()[$currOut],
        ], 'get', [], [], false);

        if ($res['httpCode'] != 200) {
            return false;
        }

        $data = [];
        $document = new \SimpleXMLElement($res['result']);

        foreach ($document->Record as $v) {
            $key = date('Y-m-d', strtotime($v->attributes()['Date']->__toString()));
            $value = str_replace(',', '.', $v->Value->__toString());
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencyCodeMap(): array
    {
        return [
            'USD' => 'R01235',
            'EUR' => 'R01239',
        ];
    }
}

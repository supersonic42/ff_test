<?php

namespace App\Controllers;

use App\App;
use App\Helpers\RequestHelper;
use App\Models\CurrencyInfoSrc\CBR;
use App\Models\CurrencyRate;

class CurrencyController
{
    public static function getRate(): void
    {
        $config = App::getInstance()->getConfig();
        $redis = App::getInstance()->getRedis();
        $redis->select($config['redis']['db_default']);

        $currencyRate = new CurrencyRate(new CBR());
        $currencyRate->dateFrom = (string) RequestHelper::getParam('date');
        $currencyRate->dateTo = date('Y-m-d', strtotime('-1 day' , strtotime($currencyRate->dateFrom)));
        $currencyRate->currIn = (string) RequestHelper::getParam('curr_in');
        $currencyRate->currOut = (string) RequestHelper::getParam('curr_out');

        if (!$currencyRate->validate()) {
            RequestHelper::responseJSON([
                'state' => 'error',
                'errors' => $currencyRate->getErrors(),
            ]);
        } else {
            $cacheKey = $currencyRate->getCacheKey();

            if ($redis->exists($cacheKey)) {
                $rate = json_decode($redis->get($cacheKey), true);
            } else {
                try {
                    $rate = $currencyRate->getRate();
                } catch (\Exception $e) {
                    RequestHelper::responseJSON([
                        'state' => 'error',
                        'error' => $e->getMessage(),
                    ]);
                }

                // Кэширование на 1 месяц
                $redis->set($cacheKey, json_encode($rate), ['nx', 'ex' => 3600 * 24 * 30]);
            }

            $data = [
                'state' => 'ok',
                'rate' => null,
                'prevDayRateDiff' => null,
            ];

            if (!empty($rate[$currencyRate->dateFrom])) {
                $data['rate'] = $rate[$currencyRate->dateFrom];

                if (!empty($rate[$currencyRate->dateTo])) {
                    $data['prevDayRateDiff'] = round($rate[$currencyRate->dateFrom] - $rate[$currencyRate->dateTo], 4);
                }
            }

            RequestHelper::responseJSON($data);
        }
    }
}

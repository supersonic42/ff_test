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
                $rate = $currencyRate->getRate();
                $redis->set($cacheKey, json_encode($rate), ['nx', 'ex' => 1]);
            }

            RequestHelper::responseJSON([
                'state' => 'ok',
                'rate' => $rate,
            ]);
        }
    }
}

<?php

namespace App\Controllers;

use App\App;
use App\Helpers\RequestHelper;
use App\Models\CurrencyRate;

class CurrencyController
{
    public static function getRate(): void
    {
        $config = App::getInstance()->getConfig();
        $redis = App::getInstance()->getRedis();
        $redis->select($config['redis']['db_default']);

        $currencyRate = new CurrencyRate();
        $currencyRate->date = (string) RequestHelper::getParam('date');
        $currencyRate->currIn = (string) RequestHelper::getParam('currIn');
        $currencyRate->currOut = (string) RequestHelper::getParam('currOut');

        if (!$currencyRate->validate()) {
            RequestHelper::responseJSON([
                'state' => 'error',
                'errors' => $currencyRate->getErrors(),
            ]);
        } else {
            $cacheKey = $currencyRate->getCacheKey();

            if ($redis->exists($cacheKey)) {
                $rate = 'cached: ' . $redis->get($cacheKey);
            } else {
                $rate = $currencyRate->getRate();
                $redis->set($cacheKey, $rate, ['nx', 'ex' => 10]);
            }

            RequestHelper::responseJSON([
                'state' => 'ok',
                'rate' => $rate,
            ]);
        }
    }
}

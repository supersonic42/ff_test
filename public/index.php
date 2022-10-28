<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\App;

$appConfig = App::getInstance()->getConfig();

if ($appConfig['env'] == 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

$router = new \Bramus\Router\Router();
$router->setNamespace('\App\Controllers');

// Route #1: Get currency rate
$router->get('/currency-rate', 'CurrencyController@getRate');

$router->run();

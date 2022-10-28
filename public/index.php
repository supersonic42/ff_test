<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\App;

$appConfig = App::getInstance()->getConfig();

if ($appConfig['env'] == 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

echo 'Hello, World!';

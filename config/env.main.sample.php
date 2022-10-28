<?php
/**
 * Config template for local development
 * Copy env.main.sample.php => env.main.php
 */
return [
    'env' => 'dev',
    'redis' => [
        'host' => 'redis',
        'port' => '6379',
        'db_default' => 0,
    ],
];

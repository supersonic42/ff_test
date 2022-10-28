<?php

namespace App;

class App {
    private static $instance = null;

    private $config = null;
    private $redis = null;
    private $rootDir = null;

    private function __construct()
    {
        $this->rootDir = dirname(__DIR__);

        $possibleMainConfigs = [
            $this->rootDir . '/config/env.main.php',
            $this->rootDir . '/config/main.php'
        ];

        foreach ($possibleMainConfigs as $filepath) {
            if (file_exists($filepath)) {
                $this->config = require $filepath;
                break;
            }
        }

        if ($this->config === null) {
            throw new \Exception('Файл главного конфига не найден');
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new App();
        }

        return self::$instance;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getRedis()
    {
        if ($this->redis === null) {
            $redis = new \Redis();
            $redis->connect($this->config['redis']['host'], $this->config['redis']['port']);
            $redis->select($this->config['redis']['db_default']);

            $this->redis = $redis;
        }

        return $this->redis;
    }
}

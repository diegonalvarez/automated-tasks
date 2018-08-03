<?php

namespace Common\Libraries\Config;

class Config
{

    private $dir;

    private $env;

    public function __construct()
    {
        $this->dir = dirname(dirname(dirname(__DIR__)));
        $this->env = getenv('APP_ENV') ?: 'development';
    }

    public function load($file)
    {
        $configFile = $file.'.'.$this->env.'.cfg';
        return parse_ini_file($configFile, true);
    }
}

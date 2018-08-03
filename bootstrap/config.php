<?php

$dir = dirname(dirname(__FILE__));
require $dir.'/vendor/autoload.php';

date_default_timezone_set(getenv('TIMEZONE'));

use Monolog\Handler\HipChatHandler;
use Monolog\Handler\SlackHandler;
use Monolog\Logger;

$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();

$log = new Logger('auto-tasks');
$handlerHipchat = new HipChatHandler(getenv('HIPCHAT_TOKEN'), getenv('HIPCHAT_CHANNEL'), 'Monolog', true, \Monolog\Logger::INFO);
$log->pushHandler($handlerHipchat);

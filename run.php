<?php
/**
 * Created by PhpStorm.
 * User: nikolatrbojevic
 * Date: 27/09/2016
 * Time: 21:48
 */
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once __DIR__ . "/vendor/autoload.php";

$config = new \CLLibs\ConnectionConfig('rabbit', 5672, 'guest', 'guest');
$logger = new Logger('');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

(new \CL\Controller(
    new \CLLibs\Queue\Queue\RabbitMQ($config, $logger),
    new \CLLibs\Messaging\Hub\RabbitMQ($config, $logger),
    $logger
))->process();


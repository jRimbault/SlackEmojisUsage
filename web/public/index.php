<?php

ini_set('error_log', __DIR__ . '/../var/log/php_error.log');

require_once __DIR__ . '/../vendor/autoload.php';


$api = new Api\Kernel();

echo $api->start();

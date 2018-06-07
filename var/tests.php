<?php

ini_set('error_log', __DIR__ . '/../var/logs/php_error.log');

require_once __DIR__ . '/../vendor/autoload.php';

use Api\Controller\Slack;
use Conserto\Http\Request;
use Api\Database\Model\Emoji;

foreach (Emoji::all() as $emoji) {
    echo $emoji->getName() . ': ' . $emoji->delta() . PHP_EOL;
}

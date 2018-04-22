<?php

ini_set('error_log', __DIR__ . '/../var/logs/php_error.log');

require_once __DIR__ . '/../vendor/autoload.php';

use Api\Model\Emoji;
use Api\Controller\Slack;
use Conserto\Http\Request;


echo (new Slack())->emojisData(new Request(), 1);

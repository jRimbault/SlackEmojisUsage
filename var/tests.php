<?php

ini_set('error_log', __DIR__ . '/../var/logs/php_error.log');

require_once __DIR__ . '/../vendor/autoload.php';

use Api\Model\Emoji;


$emojis = iterator_to_array(Emoji::getAll());
$emojis = Emoji::getAllEmojisDataOneShot();

echo json_encode(
    $emojis,
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES |
    JSON_PRETTY_PRINT
);

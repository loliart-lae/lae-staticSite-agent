<?php

require_once __DIR__ . '/Workerman/Autoloader.php';
require_once __DIR__ . '/extend/route.php';
require_once __DIR__ . '/extend/lock.php';
require_once __DIR__ . '/extend/site.php';

$token = '123456';
define('__TOKEN__', $token);

function console($msg)
{
    $date = date('Y-m-d H:i:s');
    echo PHP_EOL . "[{$date}] " . $msg . PHP_EOL;
}



if (!file_exists('core')) {
    mkdir('core');
}

if (!file_exists('core/default')) {
    mkdir('default');
}

if (!file_exists('core/sites')) {
    mkdir('sites');
}

if (!file_exists('core/www')) {
    mkdir('www');
}

<?php

require_once __DIR__ . '/Workerman/Autoloader.php';
require_once __DIR__ . '/extend/route.php';
require_once __DIR__ . '/extend/lock.php';
require_once __DIR__ . '/extend/site.php';

$token = '123456';
$port = 821;
define('__TOKEN__', $token);
define('__PORT__', $port);

function console($msg)
{
    $date = date('Y-m-d H:i:s');
    echo PHP_EOL . "[{$date}] " . $msg . PHP_EOL;
}

function getDirSize($dir)
{
    $handle = opendir($dir);
    $sizeResult = null;
    while (false !== ($FolderOrFile = readdir($handle))) {
        if ($FolderOrFile != "." && $FolderOrFile != "..") {
            if (is_dir("$dir/$FolderOrFile")) {
                $sizeResult += getDirSize("$dir/$FolderOrFile");
            } else {
                $sizeResult += filesize("$dir/$FolderOrFile");
            }
        }
    }
    closedir($handle);
    return $sizeResult;
}

function getRealSize($size)
{

    $kb = 1024; // Kilobyte
    $mb = 1024 * $kb; // Megabyte
    
    // 只需要MB
    return round($size / $mb, 2);
}

Site::count();

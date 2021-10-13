<?php

class Lock
{
    public static function lock()
    {
        $i = 0;
        if (file_exists('lock.lock')) {
            while (true) {
                $i++;
                console("[{$i}] Waiting for lock...");
                if (!file_exists('lock.lock')) {
                    file_put_contents('lock.lock', date('Y-m-d H:i:s'));
                    break;
                }

                if ($i == 10) {
                    return false;
                    break;
                }
                sleep(1);
            }
        } else {
            if (!file_exists('lock.lock')) {
                file_put_contents('lock.lock', date('Y-m-d H:i:s'));
            }
            console('Locked.');
        }
    }

    public static function release()
    {

        if (file_exists('lock.lock')) {
            unlink('lock.lock');
            console('Unlocked.');
        } else {
            console('Lock not found.');
        }
    }
}

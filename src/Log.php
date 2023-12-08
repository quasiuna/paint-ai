<?php

namespace quasiuna\paintai;

class Log
{
    public static $logPath = '';

    public static function debug($message, $level = 'debug')
    {
        if (empty(getenv('LOG'))) {
            return;
        }

        if (empty(static::$logPath)) {
            static::$logPath = ROOT . '/debug.log';
        }

        $date = date('Y-m-d H:i:s');
        $message = Cleaner::removeNewLinesFromString($message);
        $logEntry = "[$date] [$level] $message\n";
        file_put_contents(static::$logPath, $logEntry, FILE_APPEND);
    }
}

<?php

namespace quasiuna\paintai;

class Log
{
    public static $logPath = '';

    public static function debug($message, $level = 'debug', $length_limit = 120)
    {
        if (empty(getenv('LOG'))) {
            return;
        }

        if (empty(static::$logPath)) {
            static::$logPath = ROOT . '/debug.log';
        }

        $date = date('Y-m-d H:i:s');
        $message = Cleaner::removeNewLinesFromString($message);
        $logEntry = "[$date] [$level] $message";
        $logEntry = substr($logEntry, 0, $length_limit);
        $logEntry .= "\n";
        file_put_contents(static::$logPath, $logEntry, FILE_APPEND);
    }
}

<?php

namespace quasiuna\paintai;

use \PDO;

class DB
{
    public static $instance = null;
    public static $path = ROOT . '/sqlite_paint.db';

    public static function connect(): PDO
    {
        static::$instance = new PDO('sqlite:' . static::$path);
        static::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return static::$instance;
    }

    public static function query($sql, $bindings = null)
    {
        try {
            if (static::$instance === null) {
                static::connect();
            }

            $q = static::$instance->prepare($sql);
            
            if (!empty($bindings)) {
                $q->execute($bindings);
                return $q;
            } else {
                $q->execute();
                return $q;
            }
        } catch (\Exception $e) {
            Log::debug($e->getMessage(), "error");
            dd($e->getMessage());
        }
    }

    public static function exists(): bool
    {
        return is_file(static::$path);
    }

    public static function create()
    {
        static::connect();

        // Create the table if it doesn't exist
        static::$instance->exec("CREATE TABLE IF NOT EXISTS rate_limits (
            ip_address TEXT PRIMARY KEY,
            minute_count INTEGER,
            minute_timestamp INTEGER,
            hour_count INTEGER,
            hour_timestamp INTEGER
        )");

        static::$instance->exec("CREATE TABLE IF NOT EXISTS api_log (
            id INTEGER PRIMARY KEY,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            prompt_length INTEGER,
            response_length INTEGER,
            token_usage INTEGER
        )");
    }
}

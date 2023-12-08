<?php

namespace quasiuna\paintai;

use \PDO;

class RateLimiter
{
    public function canAccessAPI(): bool {
        if ($this->isLocalhost()) {
            // If the request is from localhost, don't apply rate limiting
            Log::debug("User is localhost - rate limit skipped", "ratelimit");
            return true;
        }
    
        $userIp = $this->getUserIp();
        $currentTime = time();
    
        Log::debug("[{$userIp} is user IP address ", "ratelimit");
    
        // Check if there's existing data for the user IP
        $q = DB::query("SELECT * FROM rate_limits WHERE ip_address = ?", [$userIp]);
        $rateLimit = $q->fetch(PDO::FETCH_ASSOC);
    
        if (!$rateLimit) {
            // Initialize data for new user IP
            Log::debug("[{$userIp} is a new visitor", "ratelimit");
            $rateLimit = [
                'ip_address' => $userIp,
                'minute_count' => 0,
                'minute_timestamp' => $currentTime,
                'hour_count' => 0,
                'hour_timestamp' => $currentTime
            ];
            DB::query("INSERT INTO rate_limits (ip_address, minute_count, minute_timestamp, hour_count, hour_timestamp) VALUES (:ip_address, :minute_count, :minute_timestamp, :hour_count, :hour_timestamp)", $rateLimit);
        }
    
        // Check rate limit for per minute
        if ($currentTime - $rateLimit['minute_timestamp'] < 60) {
            if ($rateLimit['minute_count'] >= 1) {
                Log::debug("[{$userIp} has exceeded minutely rate limit", "ratelimit");
                return false; // Limit exceeded for per minute
            }
        } else {
            // Reset count and timestamp for a new minute
            $rateLimit['minute_count'] = 0;
            $rateLimit['minute_timestamp'] = $currentTime;
        }
    
        // Check rate limit for per hour
        if ($currentTime - $rateLimit['hour_timestamp'] < 3600) {
            if ($rateLimit['hour_count'] >= 20) {
                Log::debug("[{$userIp} has exceeded hourly rate limit", "ratelimit");
                return false; // Limit exceeded for per hour
            }
        } else {
            // Reset count and timestamp for a new hour
            $rateLimit['hour_count'] = 0;
            $rateLimit['hour_timestamp'] = $currentTime;
        }
    
        // Increment the count for both minute and hour
        $rateLimit['minute_count']++;
        $rateLimit['hour_count']++;
    
        // Update the database
        DB::query("UPDATE rate_limits SET minute_count = :minute_count, minute_timestamp = :minute_timestamp, hour_count = :hour_count, hour_timestamp = :hour_timestamp WHERE ip_address = :ip_address", $rateLimit);
        Log::debug("[{$userIp} has m: {$rateLimit['minute_count']}, h: {$rateLimit['hour_count']}", "ratelimit");
    
        return true;
    }

    public function getUserIp(): string {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
    
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
    
        // locally, the IP can be overridden for testing
        if (!empty($_GET['ip'])) {
            return $_GET['ip'];
        }
        return '';
    }
    
    public function isLocalhost(): bool {
        return empty($this->getUserIp());
    }
}

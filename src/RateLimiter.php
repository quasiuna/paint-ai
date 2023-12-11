<?php

namespace quasiuna\paintai;

use \PDO;

class RateLimiter
{
    public $ip = '';
    private $logtag = 'ratelimit';
    private $limits = [
        'minute' => 2,
        'hour' => 30,
    ];

    public function __construct()
    {
        $this->setUserIp();

        Log::debug("User IP address is [{$this->ip}]", $this->logtag);
        Log::debug("User Identifier [{$this->getUserIdentifier()}]", $this->logtag);
    }

    public function canAccessAPI(): bool
    {
        if ($this->isLocalhost()) {
            // If the request is from localhost, don't apply rate limiting
            Log::debug("User is localhost - rate limit skipped", $this->logtag);
            return true;
        }
    
        $currentTime = time();
    
        Log::debug("[{$this->ip} is user IP address ", $this->logtag);
    
        // Check if there's existing data for the user IP
        $q = DB::query("SELECT * FROM rate_limits WHERE ip_address = ?", [$this->ip]);
        $rateLimit = $q->fetch(PDO::FETCH_ASSOC);
    
        if (!$rateLimit) {
            // Initialize data for new user IP
            Log::debug("[{$this->ip} is a new visitor", $this->logtag);
            $rateLimit = [
                'ip_address' => $this->ip,
                'minute_count' => 0,
                'minute_timestamp' => $currentTime,
                'hour_count' => 0,
                'hour_timestamp' => $currentTime
            ];
            $sql = "INSERT INTO rate_limits (ip_address, minute_count, minute_timestamp, hour_count, hour_timestamp)
            VALUES (:ip_address, :minute_count, :minute_timestamp, :hour_count, :hour_timestamp)";
            DB::query($sql, $rateLimit);
        }
    
        // Check rate limit for per minute
        if ($currentTime - $rateLimit['minute_timestamp'] < 60) {
            if ($rateLimit['minute_count'] >= $this->limits['hour']) {
                Log::debug("[{$this->ip} has exceeded minutely rate limit", $this->logtag);
                return false; // Limit exceeded for per minute
            }
        } else {
            // Reset count and timestamp for a new minute
            $rateLimit['minute_count'] = 0;
            $rateLimit['minute_timestamp'] = $currentTime;
        }
    
        // Check rate limit for per hour
        if ($currentTime - $rateLimit['hour_timestamp'] < 3600) {
            if ($rateLimit['hour_count'] >= $this->limits['hour']) {
                Log::debug("[{$this->ip} has exceeded hourly rate limit", $this->logtag);
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
        $sql = "UPDATE rate_limits
        SET minute_count = :minute_count, minute_timestamp = :minute_timestamp,
        hour_count = :hour_count, hour_timestamp = :hour_timestamp
        WHERE ip_address = :ip_address";
        DB::query($sql, $rateLimit);
        Log::debug("[{$this->ip} has m: {$rateLimit['minute_count']}, h: {$rateLimit['hour_count']}", "ratelimit");
    
        return true;
    }

    public function setUserIp(): bool
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
    
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        $this->ip = $ip;
                        return true;
                    }
                }
            }
        }
    
        // locally, the IP can be overridden for testing
        if (!empty($_GET['ip'])) {
            $this->ip = $_GET['ip'];
        }
        return true;
    }
    
    public function isLocalhost(): bool {
        return empty($this->ip);
    }

    public function getUserIdentifier(): string
    {
        if ($this->isLocalhost()) {
            return 'local';
        } else {
            return substr(md5($this->ip), 0, 8);
        }
    }
}

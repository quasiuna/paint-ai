<?php

function dd(...$var) {
    echo '<pre class="dd">';
    foreach($var as $v) {
        print_r($v);
    }
    echo '</pre>';
    exit;
}

function loadEnv()
{
    $env_path = ROOT . '/.env';

    if (!is_file($env_path)) {
        throw new \Exception("Could not find .env file");
    }

    $env = file_get_contents($env_path);

    // Split the file into lines
    $lines = explode("\n", $env);

    // Parse each line
    foreach ($lines as $line) {
        // Skip comments or empty lines
        if (empty($line) || $line[0] === '#') {
            continue;
        }

        list($name, $value) = explode("=", $line, 2);
        $name = trim($name);
        $value = trim($value);
        $_ENV[trim($name)] = trim($value);
        putenv("{$name}={$value}");
    }
}

function http(string $method, string $url, array $data, array $headers = []): array {
    $method = strtolower($method);

    $default_headers = [
        "Content-Type: application/json"
    ];

    if (empty($headers)) {
        $headers = $default_headers;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    if ($method != 'get') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new \Exception('Curl error: ' . curl_error($ch));
    } else {
        curl_close($ch);
        return json_decode($response, true);
    }
}

function removeCommentsFromJavaScript($js) {
    $pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/';
    return preg_replace($pattern, '', $js);
}

function removeNewLinesFromString($string) {
    return preg_replace('/[\s\n\r]+/', ' ', $string);
}

function removeWhitespace($string) {
    return preg_replace('/[\s\n\r]+/', '', $string);
}

function extractJs($string) {
    $pattern = '/```[A-Za-z0-9]+(.*)```/si';

    if (preg_match($pattern, $string, $match)) {
        return $match[1];
    } else {
        return $string;
    }
}

function validatePlugin($js) {
    $cleanJs = removeCommentsFromJavaScript($js);
    $noBreaks = removeNewLinesFromString($cleanJs);
    $trimmed = trim($noBreaks);
    Log::debug($trimmed);
    return preg_match('/^plugins\.[a-z0-9]+\s= class extends Tool {/i', $trimmed);
}

function getValidPluginCode($plugin) {
    if (preg_match('/^[a-z0-9]+$/i', $plugin)) {
        $path = ROOT . '/js/plugins/' . $plugin . '.js';
        if (is_file($path)) {
            $pluginJs = extractJs(file_get_contents($path));

            if (validatePlugin($pluginJs)) {
                return $pluginJs;
            } else {
                throw new \Exception('Plugin code for [' . $plugin . '] did not pass server-side validation and will not be loaded');
            }
        } else {
            throw new \Exception("Plugin does not exist");
        }
    } else {
        throw new \Exception("Plugin name invalid");
    }
}

function getUserIp(): string {
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

function isLocalhost(): bool {
    return empty(getUserIp());
}

function canAccessAPI(): bool {
    if (isLocalhost()) {
        // If the request is from localhost, don't apply rate limiting
        Log::debug("User is localhost - rate limit skipped", "ratelimit");
        return true;
    }

    $userIp = getUserIp();
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

class Log
{
    public static $logPath = '';

    public static function debug($message, $level = 'debug')
    {
        if (empty(getenv('LOG'))) {
            return;
        }

        if (empty(static::$logPath)) {
            static::$logPath = __DIR__ . '/debug.log';
        }

        $date = date('Y-m-d H:i:s');
        $message = removeNewLinesFromString($message);
        $logEntry = "[$date] [$level] $message\n";
        file_put_contents(static::$logPath, $logEntry, FILE_APPEND);
    }
}

class DB
{
    public static $instance = null;
    public static $path = ROOT . '/sqlite_paint.db';

    public static function connect(): \PDO
    {
        static::$instance = new PDO('sqlite:' . static::$path);
        static::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
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

class Analytics
{
    public static function logApiUsage($promptCharCount, $responseCharCount, $tokenUsage)
    {
        $sql = "INSERT INTO api_log (prompt_length, response_length, token_usage) VALUES (?, ?, ?)";
        return DB::query($sql, [$promptCharCount, $responseCharCount, $tokenUsage]);
    }

    // Display or process the statistics as needed
    public static function getStats($interval)
    {
        $filter = "FROM api_log WHERE timestamp >= datetime('now', ?)";
        $sql = "SELECT SUM(prompt_length) AS total_prompt_length, SUM(response_length) AS total_response_length, SUM(token_usage) AS total_tokens $filter";
        $q = DB::query($sql, [$interval]);
        $stats = $q->fetch(\PDO::FETCH_ASSOC);

        $sql = "SELECT COUNT(*) AS c $filter";
        $count = DB::query($sql, [$interval])->fetch(\PDO::FETCH_COLUMN);

        return [
            'count' => $count,
            'stats' => $stats,
        ];
    }

    public static function allStats()
    {
        $dayStats = static::getStats('-1 day');
        $weekStats = static::getStats('-7 days');
        $monthStats = static::getStats('-1 month');

        dd('1 day', "\n", $dayStats, '7 days', "\n", $weekStats, '1 month', "\n", $monthStats);
    }
}

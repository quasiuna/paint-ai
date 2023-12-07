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

class Log
{
    public static $logPath = '';

    public static function debug($message, $level = 'debug')
    {
        if (empty(static::$logPath)) {
            static::$logPath = __DIR__ . '/debug.log';
        }

        $date = date('Y-m-d H:i:s');
        $message = removeNewLinesFromString($message);
        $logEntry = "[$date] [$level] $message\n";
        file_put_contents(static::$logPath, $logEntry, FILE_APPEND);
    }
}

<?php

function dd(...$var) {
    echo '<pre class="dd">';
    foreach($var as $v) {
        print_r($v);
    }
    echo '</pre>';
    exit;
}

function loadEnv(): array
{
    if (!is_file('.env')) {
        throw new \Exception("Could not find .env file");
    }

    $env = file_get_contents('.env');

    // Split the file into lines
    $lines = explode("\n", $env);

    // Initialize an associative array to hold our variables
    $config = [];

    // Parse each line
    foreach ($lines as $line) {
        // Skip comments or empty lines
        if (empty($line) || $line[0] === '#') {
            continue;
        }

        // Split each line into name and value
        list($name, $value) = explode("=", $line, 2);

        // Store them in the config array
        $config[trim($name)] = trim($value);
    }

    return $config;
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

function validatePlugin($js) {
    $cleanJs = removeCommentsFromJavaScript($js);
    $noBreaks = preg_replace('/[\s\n\r]+/', ' ', $cleanJs);
    $trimmed = trim($noBreaks);
    // dd($trimmed);
    return preg_match('/^plugins\.[a-z]+\s= class extends Tool {/i', $trimmed);
}
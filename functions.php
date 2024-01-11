<?php

use quasiuna\paintai\Provider;

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

function parseRawJsonRequest(): array {
    $incoming_data = file_get_contents('php://input');
    $data = json_decode($incoming_data, true);
    return is_array($data) ? $data : [];
}

function getProvider(string $class_name): Provider {
    $class = 'quasiuna\paintai\Providers\\' . $class_name;
    return new $class;
}

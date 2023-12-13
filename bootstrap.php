<?php
use quasiuna\paintai\AiScript;
use quasiuna\paintai\RateLimiter;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header("Access-Control-Allow-Credentials: true");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    exit;
}

require 'vendor/autoload.php';
require 'functions.php';

define('ROOT', __DIR__);
define('WWW', ROOT . '/www');
loadEnv();

$uid = $_SESSION['uid'] ?? null;

if (empty($uid)) {
    session_start();
    $rateLimiter = new RateLimiter;
    $_SESSION['uid'] = $rateLimiter->getUserIdentifier();
} else {
    session_start([
        'read_and_close' => true,
    ]);
}

if (empty($_SESSION['uid'])) {
    throw new \Exception("Error: invalid user session");
}

$ais = new AiScript(['user' => $_SESSION['uid']]);

$existing_plugins = glob($ais->getOutputDir() . '/*.js');
$existing_plugins = array_values(array_filter(array_map(function($p) {
    $name = preg_replace('|^.*\/(.+)\.js$|', "$1", $p);

    return [
        'path' => $p,
        'name' => $name,
    ];
}, $existing_plugins)));

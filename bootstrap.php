<?php
use quasiuna\paintai\AiScript;
use quasiuna\paintai\RateLimiter;

set_error_handler(function($errno, $errstr, $errfile, $errline ){
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
});

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header("Access-Control-Allow-Credentials: true");

$method = $_SERVER['REQUEST_METHOD'] ?? '';
if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    exit;
}

require 'vendor/autoload.php';
require 'functions.php';

define('ROOT', __DIR__);
define('WWW', ROOT . '/www');
loadEnv();

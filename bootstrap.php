<?php
use quasiuna\paintai\AiScript;
use quasiuna\paintai\RateLimiter;

require 'vendor/autoload.php';
require 'functions.php';

// dd("TODO: make it so the plugin class name and file name ALWAYS MATCH");
define('ROOT', __DIR__);
define('WWW', ROOT . '/www');
loadEnv();

$rateLimiter = new RateLimiter;
$ais = new AiScript(['user' => $rateLimiter->getUserIdentifier()]);

$existing_plugins = glob($ais->getOutputDir() . '/*.js');
$existing_plugins = array_values(array_filter(array_map(function($p) {
    $name = preg_replace('|^.*\/(.+)\.js$|', "$1", $p);

    // if (!in_array($name, ['PenTool', 'EmojiTool', 'EggTool'])) {
    // if (!in_array($name, ['Pencil2', 'Highlighter'])) {
    //     return '';
    // }

    return [
        'path' => $p,
        'name' => $name,
    ];
}, $existing_plugins)));

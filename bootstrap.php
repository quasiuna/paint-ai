<?php

require 'vendor/autoload.php';
require 'functions.php';

loadEnv();

$existing_plugins = glob('./plugins/*.js');
$existing_plugins = array_map(function($p) {
    return [
        'path' => $p,
        'name' => preg_replace('|^.*\/(.+)\.js$|', "$1", $p),
    ];
}, $existing_plugins);

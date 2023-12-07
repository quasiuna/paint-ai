<?php

require 'vendor/autoload.php';
require 'functions.php';

define('ROOT', __DIR__);

loadEnv();

$existing_plugins = glob(ROOT . '/js/plugins/*.js');
$existing_plugins = array_map(function($p) {
    $name = preg_replace('|^.*\/(.+)\.js$|', "$1", $p);

    return [
        'path' => $p,
        'name' => $name,
    ];
}, $existing_plugins);

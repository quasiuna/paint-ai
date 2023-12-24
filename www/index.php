<?php

require '../bootstrap.php';
require 'controllers/Controller.php';

// bypass anything that looks like a file
$url = explode('?', $_SERVER['REQUEST_URI'] ?? '');

if (preg_match('/\.[a-z]{2,4}$/i', $url[0])) {
    return false;
}


/**
 * URL Parsing & Routing
 * - URL is expected to be in the form /{controller}/{param}?{query}
 * - Controller will be loaded automatically
 * - Action defaults to "index" if not provided
 * - Action will be translated according to REST principles:
 *   - GET requests with no param -> index
 *   - GET requests with param -> show
 *   - POST requests with no param -> store
 *   - POST/PUT/PATCH requests with params-> update
 */
$url_parts = array_values(array_filter(explode('/', $url[0])));
$url_parts += ['home', 'index']; // default controller/view

$controller_name = ucwords($url_parts[0], '-') . 'Controller';
$action = $url_parts[1];


if (in_array(strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
    if ($action != 'index') {
        $action = 'update';
    } else {
        $action = 'store';
    }
} elseif ($action != 'index') {
    $action = 'show';
}

if (!in_array($action, ['index', 'show', 'store', 'update'])) {
    throw new \Exception("Routing Error - Invalid Action: [$action]");
}

require 'controllers/' . $controller_name . '.php';

$controller = new $controller_name;
$controller->{$action}($url_parts[1] ?? '');

function view($name = '', $variables = []) {
    $path = WWW . '/views/' . $name . '.php';
    
    if (!is_file($path)) {
        throw new \Exception("Routing Error - View not found: [$name]");
    }

    extract($variables);
    require $path;
}

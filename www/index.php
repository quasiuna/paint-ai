<?php
use quasiuna\paintai\RateLimiter;
use quasiuna\paintai\Log;

require '../bootstrap.php';

Log::debug("--- New Request ---");

try {
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
     *   - DELETE requests with params-> destroy
     */
    $url_parts = array_values(array_filter(explode('/', $url[0])));
    $url_parts += ['home', 'index']; // default controller/view
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $controller_name = ucwords($url_parts[0], '-') . 'Controller';
    $action = $url_parts[1];

    if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
        if ($action != 'index') {
            if ($method == 'DELETE') {
                $action = 'destroy';
            } else {
                $action = 'update';
            }
        } else {
            $action = 'store';
        }
    } elseif ($action != 'index') {
        $action = 'show';
    }

    if (!in_array($action, ['index', 'show', 'store', 'update', 'destroy'])) {
        throw new \Exception("Routing Error - Invalid Action: [$action]");
    }

    require WWW . '/controllers/' . $controller_name . '.php';

    $controller = new $controller_name;
    $controller->params = parseRawJsonRequest();
    $controller->{$action}($url_parts[1] ?? '');
} catch (\Throwable $e) {
    $status_code = 500;
    header("{$_SERVER['SERVER_PROTOCOL']} $status_code Internal Server Error", true, $status_code);
    exit(json_encode([
        'error' => $e->getMessage(),
        'message' => str_replace(ROOT, '', $e->getFile()) . ':' . $e->getLine(),
        'status' => $status_code,
        'request' => [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
            'path' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'params' => $controller->params ?? [],
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

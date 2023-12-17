<?php

use quasiuna\paintai\AiScript;
use quasiuna\paintai\Cleaner;
use quasiuna\paintai\RateLimiter;
use quasiuna\paintai\Log;

require '../bootstrap.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

switch ($_GET['method'] ?? null) {
    case 'load':
        if (!empty($_GET['plugin'])) {
            $plugin = $_GET['plugin'];

            try {
                $limitedUser = new RateLimiter;
                $ai = new AiScript([
                    'name' => $plugin,
                    'user' => $limitedUser->getUserIdentifier(),
                ]);
                $code = $ai->getValidPluginCode($plugin);
                exit(json_encode(['tool' => $ai->getClass(), 'pluginCode' => $code]));
            } catch (\Exception $e) {
                exit(json_encode(['error' => $e->getMessage()]));
            }
        }
        break;
    case 'ai':
        $limitedUser = new RateLimiter;

        if (!$limitedUser->canAccessAPI()) {
            //TODO: handle this situation more gracefully in terms of the UX - "e.g. please wait for X seconds"
            throw new \Exception("Rate Limit Exceeded");
        }

        $params = parseRawJsonRequest();
        $params['user'] = $limitedUser->getUserIdentifier();

        Log::debug(json_encode($params));
        $ai = new AiScript($params);
        $script = $ai->create();

        exit(json_encode(['tool' => $ai->getClass(), 'pluginCode' => $script]));

        break;
    case 'delete':

        $limitedUser = new RateLimiter;

        if (!$limitedUser->canAccessAPI()) {
            //TODO: handle this situation more gracefully in terms of the UX - "e.g. please wait for X seconds"
            throw new \Exception("Rate Limit Exceeded");
        }

        $params = parseRawJsonRequest();
        $params['user'] = $limitedUser->getUserIdentifier();

        Log::debug(json_encode($params), "delete");
        $ai = new AiScript($params);
        exit(json_encode(['delete' => (int) $ai->delete($params['name'])]));
        break;
    default:
        exit('404 - Method not found');
        break;
}


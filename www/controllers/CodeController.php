<?php

use quasiuna\paintai\Controller;
use quasiuna\paintai\Plugins\PaintTool;
use quasiuna\paintai\Cleaner;
use quasiuna\paintai\RateLimiter;
use quasiuna\paintai\Log;

class CodeController extends Controller
{
    public function index()
    {
    }

    public function show($arg)
    {
        try {
            $limitedUser = new RateLimiter;
            $plugin = new PaintTool([
                'name' => $arg,
                'user' => $limitedUser->getUserIdentifier(),
            ]);

            $this->respond([
                'tool' => $plugin->getClass(),
                'pluginCode' => $plugin->getCode(),
            ]);
        } catch (\Exception $e) {
            exit(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function store()
    {
        $limitedUser = new RateLimiter;

        if (!$limitedUser->canAccessAPI()) {
            //TODO: handle this situation more gracefully in terms of the UX - "e.g. please wait for X seconds"
            throw new \Exception("Rate Limit Exceeded");
        }

        $this->params['user'] = $limitedUser->getUserIdentifier();

        Log::debug(json_encode($this->params));
        $plugin = new PaintTool($this->params);
        $script = $plugin->create();

        $this->respond(['tool' => $plugin->getClass(), 'pluginCode' => $script]);
    }

    public function update($arg)
    {
        $limitedUser = new RateLimiter;

        if (!$limitedUser->canAccessAPI()) {
            //TODO: handle this situation more gracefully in terms of the UX - "e.g. please wait for X seconds"
            throw new \Exception("Rate Limit Exceeded");
        }

        $this->params['user'] = $limitedUser->getUserIdentifier();
        $this->params['name'] = $arg;

        Log::debug(json_encode($this->params));
        $plugin = new PaintTool($this->params);
        $script = $plugin->edit();

        $this->respond(['tool' => $plugin->getClass(), 'pluginCode' => $script]);
        throw new \Exception("Not done yet");
    }

    public function destroy($arg)
    {
        $limitedUser = new RateLimiter;

        if (!$limitedUser->canAccessAPI()) {
            //TODO: handle this situation more gracefully in terms of the UX - "e.g. please wait for X seconds"
            throw new \Exception("Rate Limit Exceeded");
        }

        $this->params['user'] = $limitedUser->getUserIdentifier();
        $this->params['name'] = $arg;

        Log::debug(json_encode($this->params), "delete");
        $plugin = new PaintTool($this->params);
        $result = (int) $plugin->delete($this->params['name']);

        if ($result) {
            $message = "was deleted";
        } else {
            $message = "was not deleted, or could not be found";
        }

        $this->respond([
            'delete' => $result,
            'message' => "Plugin {$this->params['name']} $message"
        ]);
    }
}

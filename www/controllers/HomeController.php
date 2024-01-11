<?php

use quasiuna\paintai\Controller;
use quasiuna\paintai\Plugins\PaintTool;

class HomeController extends Controller
{
    public function index()
    {
        $plugin = new PaintTool(['user' => $_SESSION['uid']]);
        $existing_plugins = $plugin->listAllPlugins();

        return $this->view('home.index', compact('existing_plugins'));
    }

    public function show($arg)
    {
    }

    public function store()
    {
    }

    public function update($arg)
    {
    }

    public function destroy($arg)
    {
    }
}
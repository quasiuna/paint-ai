<?php

namespace quasiuna\paintai;

abstract class Controller
{
    abstract public function index();
    abstract public function show($arg);
    abstract public function store();
    abstract public function update($arg);
    abstract public function destroy($arg);

    public $params = [];

    public function view($name = '', $variables = [])
    {
        $path = WWW . '/views/' . $name . '.php';

        if (!is_file($path)) {
            throw new \Exception("Routing Error - View not found: [$name]");
        }

        extract($variables);
        require $path;
    }

    public function respond($response)
    {
        exit(json_encode($response));
    }
}

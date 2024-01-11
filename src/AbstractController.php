<?php

class AbstractController
{
    public function view($name = '', $variables = [])
    {
        $path = WWW . '/views/' . $name . '.php';

        if (!is_file($path)) {
            throw new \Exception("Routing Error - View not found: [$name]");
        }

        extract($variables);
        require $path;
    }
}

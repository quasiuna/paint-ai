<?php

namespace quasiuna\paintai\Plugins;

use quasiuna\paintai\Plugin;

class Text extends Plugin
{
    public $stop = ['.', ','];

    public function validate(string $code): string
    {
        if (!empty($code)) {
            return $code;
        }

        return '';
    }

    public function deploy()
    {
        $output = "Text Plugin Code:\n";
        $output .= $this->code;
        $output .= "\n";

        return $output;
    }
}

<?php

namespace quasiuna\paintai\Plugins;

use quasiuna\paintai\Plugin;

class Text extends Plugin
{
    public $defaults = [
        'output_dir' => WWW . '/text/plugins',
        'prompt_file' => 'Text_1.txt',
    ];

    public $file_extension = '.txt';

    public $stop = ['.', ','];

    public function validate(string $code): string
    {
        if (!empty($code)) {
            return $code;
        }

        return '';
    }

    public function deploy(): bool
    {
        $output = "Text Plugin Code:\n";
        $output .= $this->code;
        $output .= "\n";

        return $output;
    }
}

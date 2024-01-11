<?php

namespace quasiuna\paintai\Plugins;

use quasiuna\paintai\Cleaner;
use quasiuna\paintai\Log;
use quasiuna\paintai\Plugin;

class PaintTool extends Plugin
{
    public $name = '';
    public $description = '';
    public $config = [];
    public $provider = null;
    public $prompt = null;
    public $start = '```javascript';
    public $stop = ['```', "`\n``"];
    private $classNamePattern = '[A-Za-z0-9_]+';
    public $file_extension = '.js';

    public $defaults = [
        'prompt_file' => 'PaintTool_1.txt',
        'output_dir' => WWW . '/js/plugins',
    ];

    public function validate(string $code): string
    {
        $pluginJs = $this->extractJs($code);

        if ($this->validatePlugin($pluginJs)) {
            return $pluginJs;
        } else {
            throw new \Exception('Plugin code for [' . $this->getClass() . '] did not pass server-side validation and will not be loaded');
        }
    }

    public function deploy(): bool
    {
        // ensure our file name matches the JS class
        $nameFromCode = $this->getNameFromCode();

        if ($nameFromCode != $this->getClass()) {
            $this->name = $nameFromCode;
        }

        if ($nameFromCode != $this->getClass()) {
            throw new \Exception("Deployment failed: Class name does not match tool name");
        }

        $target_path = $this->getNextVersionPath();
        $this->code = preg_replace('/^(plugins\.)(' . $this->classNamePattern . ')(\s= class extends Tool)/i', "$1{$this->getClass()}$3", $this->code);

        if (!$this->validatePlugin($this->code)) {
            throw new \Exception("Deployment failed: Code is invalid");
        }

        if (is_file($target_path)) {
            throw new \Exception("Deployment failed: Target path is not unique");
        }

        $this->writeFile($target_path, $this->code);

        Log::debug(Cleaner::removeCommentsFromJavaScript($this->code));

        return file_exists($target_path);
    }

    public function edit(array $messages = []): string
    {
        $this->config['prompt_file'] = 'PaintTool_Edit_2.txt';
        return parent::edit($messages);
    }

    public function extractJs(string $string): string
    {
        $pattern = '/```[A-Za-z0-9]+(.*)```/si';

        // remove any ```javascript ... ``` markdown
        if (preg_match($pattern, $string, $match)) {
            $string = $match[1];
        }

        // remove anything before "plugins"
        $string = preg_replace('/.*plugins\./si', 'plugins.', $string);
        return trim($string);
    }

    public function validatePlugin($js) 
    {
        $cleanJs = Cleaner::removeCommentsFromJavaScript($js);
        $noBreaks = Cleaner::removeNewLinesFromString($cleanJs);
        $trimmed = trim($noBreaks);
        Log::debug($trimmed);
        return preg_match('/^plugins\.' . $this->classNamePattern . '\s= class extends Tool {/i', $trimmed);
    }

    public function getNameFromCode(): string
    {
        if (preg_match('/^plugins\.(' . $this->classNamePattern . ')\s= class extends Tool/i', $this->code, $match)) {
            return $match[1];
        } else {
            return '';
        }
    }
}

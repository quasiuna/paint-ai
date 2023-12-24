<?php

namespace quasiuna\paintai\Plugins;

use Orhanerday\OpenAi\OpenAi;
use quasiuna\paintai\Cleaner;
use quasiuna\paintai\Log;

class PaintTool extends Plugin
{
    public $name = '';
    public $description = '';
    public $config = [];
    public $provider = null;
    public $prompt = null;

    public $start = '```javascript';
    public $stop = ['```'];

    private $classNamePattern = '[A-Za-z0-9_]+';

    public function validate(string $code): string
    {
        return $code;
    }

    public function deploy()
    {
    }

    public function getOutputDir()
    {
        return $this->config['output_dir'] . '/' . $this->getUserDir();
    }

    public function getPluginPath(string $plugin): string
    {
        $path = $this->getOutputDir() . '/' . $plugin . '.js';
        // Log::debug("path will be: {$path}");
        return $path;
    }

    public function getArchivePath(string $plugin): string
    {
        return $this->getOutputDir() . '/archive/' . $plugin . '.js';
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

    public function getValidPluginCode($plugin)
    {
        if (preg_match('/^' . $this->classNamePattern . '$/i', $plugin)) {
            $path = $this->getPluginPath($plugin);
            if (is_file($path)) {
                $pluginJs = $this->extractJs(file_get_contents($path));
    
                if ($this->validatePlugin($pluginJs)) {
                    return $pluginJs;
                } else {
                    throw new \Exception('Plugin code for [' . $plugin . '] did not pass server-side validation and will not be loaded');
                }
            } else {
                dd($path);
                throw new \Exception("Plugin does not exist");
            }
        } else {
            throw new \Exception("Plugin name invalid");
        }
    }

    public function validatePlugin($js) 
    {
        $cleanJs = Cleaner::removeCommentsFromJavaScript($js);
        $noBreaks = Cleaner::removeNewLinesFromString($cleanJs);
        $trimmed = trim($noBreaks);
        Log::debug($trimmed);
        return preg_match('/^plugins\.' . $this->classNamePattern . '\s= class extends Tool {/i', $trimmed);
    }

    public function parseTextToRoleContentArray($text)
    {
        $lines = explode("\n", $text);
        $result = [];
        $currentRole = '';
        $currentContent = '';
    
        foreach ($lines as $line) {
            if (empty($line)) {
                $currentContent .= "\n";
                continue;
            }
    
            if (strpos($line, '#') === 0) {
                // If a new role is encountered, save the current content (if any) and update the current role.
                if (!empty($currentContent)) {
                    $result[] = [
                        'role' => trim($currentRole),
                        'content' => trim($currentContent),
                    ];
                    $currentContent = '';
                }

                $currentRole = trim(strtolower(preg_replace('/^[# ]+/', '', $line)));
            } else {
                // Append the line to the current content.
                $currentContent .= $line . "\n";
            }
        }
    
        // Save the last content (if any).
        if (!empty($currentContent)) {
            $result[] = [
                'role' => $currentRole,
                'content' => $currentContent
            ];
        }
    
        return $result;
    }

    public function saveCode($code)
    {
        $code = $this->extractJs($code);

        $valid = $this->validate($code);

        if (!$valid) {
            throw new \Exception("Cannot save invalid code");
        }

        // ensure our file name matches the JS class
        $nameFromCode = $this->getNameFromCode($code);

        if ($nameFromCode != $this->getClass()) {
            $this->name = $nameFromCode;
        }

        if ($nameFromCode != $this->getClass()) {
            throw new \Exception("Class name does not match tool name - cannot continue");
        }

        $target_path = $this->getPluginPath($this->getClass());

        if (is_file($target_path)) {
            $this->name = $this->name . '_' . time();
            $code = preg_replace('/^(plugins\.)(' . $this->classNamePattern . ')(\s= class extends Tool)/i', "$1{$this->name}$3", $code);

            $valid = $this->validatePlugin($code);

            if (!$valid) {
                throw new \Exception("Code is no longer valid after renaming class. Cannot save.");
            }

            $target_path = $this->getPluginPath($this->getClass());

            if (is_file($target_path)) {
                throw new \Exception("After renaming the target path is still not unique. Ending here");
            }
        }

        $this->writeFile($target_path, $code);

        Log::debug(Cleaner::removeCommentsFromJavaScript($code));
    }

    public function getNameFromCode(string $code): string
    {
        if (preg_match('/^plugins\.(' . $this->classNamePattern . ')\s= class extends Tool/i', $code, $match)) {
            return $match[1];
        } else {
            return '';
        }
    }

    public function delete(string $tool_name): bool
    {
        $this->name = $tool_name;
        $target_path = $this->getPluginPath($this->getClass());

        if (!preg_match('/\.js$/', $target_path)) {
            return false;
        }

        if (is_file($target_path)) {
            $code = file_get_contents($target_path);
            $archive_path = $this->getArchivePath($this->getClass());
            $this->writeFile($archive_path, $code);
            return unlink($target_path);
        }

        return false;
    }
}

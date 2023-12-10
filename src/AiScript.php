<?php

namespace quasiuna\paintai;

use \OpenAI;

/**
 * A block of code created by an AI
 */
class AiScript
{
    public $name = '';
    public $description = '';
    public $config = [];

    private $classNamePattern = '[A-Za-z0-9_]+';

    public function __construct(array $config)
    {
        $defaults = [
            'prompt_file' => ROOT . '/prompt.txt',
            'output_dir' => WWW . '/js/plugins',
        ];

        $config += $defaults;

        if (!empty($config['name'])) {
            $this->name = trim(Cleaner::removeNewLinesFromString($config['name']));
        } else {
            throw new \Exception("A name is required");
        }

        if (!empty($config['description'])) {
            $this->description = trim(Cleaner::removeNewLinesFromString($config['description']));
        }

        $this->config = $config;

        if (!is_file($this->config['prompt_file'])) {
            throw new \Exception("Prompt file does not exist");
        }
    }

    public function getOutputDir()
    {
        return $this->config['output_dir'];
    }

    public function getPrompt()
    {
        return file_get_contents($this->config['prompt_file']);
    }

    public function getFullPrompt()
    {
        return preg_replace('/{{USER_PROMPT}}/i', $this->getUserPrompt(), $this->getPrompt());
    }

    public function getPromptMessages()
    {
        return $this->parseTextToRoleContentArray($this->getFullPrompt());
    }

    public function getUserPrompt()
    {
        return $this->name . ': ' . $this->description;
    }

    public function getClass()
    {
        return Cleaner::removeWhitespace($this->name);
    }

    public function create()
    {
        if (empty($this->description)) {
            throw new \Exception("Error - no description has been provided for this script");
        }

        $api_key = getenv('OPENAI_API_KEY');
        $client = OpenAI::client($api_key);

        Log::debug('Requesting code from OpenAI model:' . getenv('OPENAI_MODEL_PRIMARY'));

        $response = '';

        try {
            $params = [
                'model' => getenv('OPENAI_MODEL_PRIMARY'),
                'messages' => $this->getPromptMessages(),
                'stop' => [
                    '```',
                ]
            ];

            Log::debug('Sending request to OpenAI');
            Log::debug(json_encode($params));
            $result = $client->chat()->create($params);

            $response = $result->choices[0]->message->content ?? '';
            Log::debug('Response received from OpenAI');
            Log::debug(json_encode($result));
            Log::debug($response);

            $response = trim($this->extractJs($response));

            $this->saveCode($response);
            
            Log::debug(Cleaner::removeCommentsFromJavaScript($response));
        } catch (\Exception $e) {
            Log::debug("AI API ERROR: " . $e->getMessage(), 'error');
        }

        $responseCharCount = strlen($response);
        $tokenUsage = $result->usage->totalTokens ?? 0;

        Analytics::logApiUsage(strlen($this->getFullPrompt()), $responseCharCount, $tokenUsage);
        Log::debug($tokenUsage . ' tokens used');

        try {
            return $this->getValidPluginCode($this->getClass());
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function response()
    {
    }

    public function extractJs($string)
    {
        $pattern = '/```[A-Za-z0-9]+(.*)```/si';

        if (preg_match($pattern, $string, $match)) {
            return $match[1];
        } else {
            return $string;
        }
    }

    public function getValidPluginCode($plugin)
    {
        if (preg_match('/^' . $this->classNamePattern . '$/i', $plugin)) {
            $path = WWW . '/js/plugins/' . $plugin . '.js';
            if (is_file($path)) {
                $pluginJs = $this->extractJs(file_get_contents($path));
    
                if ($this->validatePlugin($pluginJs)) {
                    return $pluginJs;
                } else {
                    throw new \Exception('Plugin code for [' . $plugin . '] did not pass server-side validation and will not be loaded');
                }
            } else {
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
        $valid = $this->validatePlugin($code);

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

        $target_path = $this->getOutputDir() . '/' . $this->getClass() . '.js';

        if (is_file($target_path)) {
            $this->name = $this->name . '_' . time();
            $code = preg_replace('/^(plugins\.)(' . $this->classNamePattern . ')(\s= class extends Tool)/i', "$1{$this->name}$3", $code);

            $valid = $this->validatePlugin($code);

            if (!$valid) {
                throw new \Exception("Code is no longer valid after renaming class. Cannot save.");
            }

            $target_path = $this->getOutputDir() . '/' . $this->getClass() . '.js';

            if (is_file($target_path)) {
                throw new \Exception("After renaming the target path is still not unique. Ending here");
            }
        }

        file_put_contents($target_path, $code);
    }

    public function getNameFromCode(string $code): string
    {
        if (preg_match('/^plugins\.(' . $this->classNamePattern . ')\s= class extends Tool/i', $code, $match)) {
            return $match[1];
        } else {
            return '';
        }
    }
}

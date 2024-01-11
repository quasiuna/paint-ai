<?php

namespace quasiuna\paintai;

use quasiuna\paintai\Providers\LMStudio;

abstract class Plugin
{
    public $mode = 'create';
    public $code = '';
    public $start = '';
    public $stop = '';
    public $messages = [];
    public $provider = null;
    public $name = '';
    public $description = '';
    public $config = [];
    public $file_extension = '';
    public $defaults = [];

    public function setMessages(array $messages)
    {
        $this->messages = $messages;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Each plugin must create its own validation function
     * to verify whether the source code written by the AI
     * looks ok, or not. If it is ok, the valid code is returned.
     * Otherwise an empty string is returned.
     */
    abstract public function validate(string $code): string;

    /**
     * Each plugin must create its own deploy function which
     * determines what happens to $this->code at after it has
     * been validated
     * 
     * The function returns a boolean success response
     */
    abstract public function deploy(): bool;

    public function __construct(array $config = [])
    {
        $config += $this->defaults;

        if (empty($config['provider'])) {
            $this->provider = new LMStudio;
        } else {
            $this->provider = $config['provider'];
        }

        if (empty($config['user'])) {
            throw new \Exception("A user is required");
        }

        if (!empty($config['name'])) {
            $this->name = trim(Cleaner::removeNewLinesFromString($config['name']));
        }

        if (!empty($config['description'])) {
            $this->description = trim(Cleaner::removeNewLinesFromString($config['description']));
        }

        $this->config = $config;

        if (!is_file($this->getPromptPath())) {
            throw new \Exception("Prompt file does not exist");
        }
    }

    public function getCode()
    {
        $code = file_get_contents($this->getPluginPath());
        $code = $this->validate($code);

        return $code;    
    }

    public function createOrUpdate(array $messages = []): string
    {
        if (empty($this->description)) {
            throw new \Exception("Error - no description has been provided for this script");
        }

        $api_key = getenv('OPENAI_API_KEY');

        $params = [
            'api_key' => $api_key,
        ];
        $client = $this->provider->client($params);

        Log::debug('Requesting code from OpenAI model:' . getenv('OPENAI_MODEL_PRIMARY'), $this->mode);

        $response = '';

        try {
            if ($this->mode == 'edit') {
                $this->code = $this->getCode();

                if (empty($this->code)) {
                    throw new \Exception("Code is empty - cannot " . $this->mode);
                }
            }

            $params = [
                'model' => getenv('OPENAI_MODEL_PRIMARY'),
                'messages' => $messages ?: $this->getPromptMessages(),
                'stop' => $this->stop,
            ];

            // dd('mode: ' . $this->mode, $this->getOriginalUserPrompt(),  $params);

            Log::debug('Sending ' . $this->mode . ' request to AI', $this->mode);
            Log::debug(json_encode($params), $this->mode);
            $result = json_decode($client->chat($params));

            $response = $result->choices[0]->message->content ?? '';
            file_put_contents(ROOT . "/api.log", date("Y-m-d H:i:s"). "\n\n" . $response . "\n\n===\n", FILE_APPEND);
            Log::debug($this->mode . ' response received from OpenAI', $this->mode);
            Log::debug(json_encode($result), $this->mode);

            $responseCharCount = strlen($response);
            $tokenUsage = $result->usage->totalTokens ?? 0;

            if ($this->code = $this->validate($response)) {
                if ($this->deploy()) {
                    if (!empty($params['model'])) {
                        Analytics::logApiUsage($this->getUserDirName(), $params['model'], strlen($this->promptWithReplacedVariables()), $responseCharCount, $tokenUsage, $this->getUserPrompt(), $this->name);
                        Log::debug($tokenUsage . ' tokens used', $this->mode);
                    }

                    return $this->code;
                }
            }
        } catch (\Exception $e) {
            Log::debug("AI API ERROR: " . $e->getMessage(), $this->mode . ' error');
            throw $e;
        }

        throw new \Exception("Code create failed. Code was not successfully written, validated and deployed.");
    }

    /**
     * Send a prompt to an AI and return a valid piece of source code
     */
    public function create(array $messages = []): string
    {
        $this->mode = 'create';
        return $this->createOrUpdate($messages);
    }

    /**
     * Get an AI to refactor/change existing code
     */
    public function edit(array $messages = []): string
    {
        $this->mode = 'edit';
        return $this->createOrUpdate($messages);
    }

    public function promptWithReplacedVariables()
    {
        $prompt = $this->getPrompt();

        $variables = [
            'USER_PROMPT' => $this->getUserPrompt(),
            'EXISTING_CODE' => $this->code,
        ];

        if ($this->mode == 'edit') {
            $variables['ORIGINAL_USER_PROMPT'] = $this->getOriginalUserPrompt();
        }

        foreach ($variables as $key => $value) {
            $prompt = preg_replace('/{{' . $key . '}}/i', $value, $prompt);
        }

        return $prompt;
    }

    public function getUserDirName(): string
    {
        return $this->config['user'];
    }

    public function writeFile($path, $string): bool
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        
        return (bool) file_put_contents($path, $string);
    }

    public function getPromptPath()
    {
        return ROOT . '/prompts/' . $this->config['prompt_file'];
    }

    public function getPrompt()
    {
        return file_get_contents($this->getPromptPath());
    }

    public function getPromptMessages()
    {
        return $this->parseTextToRoleContentArray($this->promptWithReplacedVariables());
    }

    public function getUserPrompt()
    {
        if ($this->mode == 'edit') {
            return $this->description;
        } else {
            return $this->name . ': ' . $this->description;
        }
    }

    /**
     * When editing existing code, get the original prompt
     * used to generate the first version of the code
     */
    public function getOriginalUserPrompt(): string
    {
        return Analytics::getPrompt($this->getUserDirName(), $this->name) ?: '';
    }

    public function getClass()
    {
        if (empty($this->name)) {
            throw new \Exception("Error: No name has been provided for this script");
        }

        return Cleaner::removeWhitespace($this->name);
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

    public function getOutputDir()
    {
        if (empty($this->config['output_dir'])) {
            throw new \Exception("No output_dir specified in plugin config");
        }

        return $this->config['output_dir'] . '/' . $this->getUserDirName();
    }

    public function delete(string $tool_name): bool
    {
        $this->name = $tool_name;
        $target_path = $this->getPluginPath($this->getClass());

        if (!preg_match('/' . preg_quote($this->file_extension) . '$/', $target_path)) {
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

    public function getPluginPath(?string $plugin = null): string
    {
        $plugin = $plugin ?? $this->getClass();

        return $this->getOutputDir() . '/' . $plugin . $this->file_extension;
    }

    public function getArchivePath(string $plugin): string
    {
        return $this->getOutputDir() . '/archive/' . $plugin . $this->file_extension;
    }

    public function listAllPlugins()
    {
        $existing_plugins = glob($this->getOutputDir() . '/*' . $this->file_extension);
        $existing_plugins = array_values(array_filter(array_map(function ($p) {
            $name = preg_replace('|^.*\/(.+)' . preg_quote($this->file_extension) . '$|', "$1", $p);
            return [
                'path' => $p,
                'name' => $name,
            ];
        }, $existing_plugins)));

        return $existing_plugins;
    }

    public function getNextVersionPath()
    {
        $target_path = $this->getPluginPath($this->getClass());

        // check if this is a duplicate
        if (is_file($target_path)) {
            $this->name = $this->getNextVersionName();
            $target_path = $this->getPluginPath($this->getClass());
        }

        return $target_path;
    }

    private function getUnversionedName(): string
    {
        if (preg_match('/^([^_]+)(_[0-9]+)?$/', $this->name, $match)) {
            return $match[1];
        } else {
            return $this->name;
        }
    }

    public function getNextVersionName(): string
    {
        $max = 0;
        $numbers = [];
        $plugins = $this->listAllPlugins();
        $unversioned_name = $this->getUnversionedName();

        if (!empty($plugins)) {
            foreach ($plugins as $plugin) {
                if (preg_match('/^' . preg_quote($unversioned_name) . '(_[0-9]+)?$/', $plugin['name'], $match)) {
                    $number = (preg_replace('/[^0-9]+/', '', $match[1] ?? '')) ?: 0;
                    $numbers[] = $number;
                } 
            }
        }

        if (!empty($numbers)) {
            $max = max($numbers);
        }

        return $unversioned_name . '_' . ($max + 1);
    }
}

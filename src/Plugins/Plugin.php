<?php

namespace quasiuna\paintai\Plugins;
use quasiuna\paintai\Cleaner;
use quasiuna\paintai\Log;
use quasiuna\paintai\Analytics;
use quasiuna\paintai\Providers\LMStudio;

class Plugin
{
    public $code = '';
    
    public $start = '';
    public $stop = '';
    public $messages = [];

    public $provider = null;

    public $name = '';
    public $description = '';
    public $config = [];

    public function setMessages(array $messages)
    {
        $this->messages = $messages;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function validate(string $code): string
    {
    }

    public function deploy()
    {
    }

    public function __construct(array $config = [])
    {
        $defaults = [
            'prompt_file' => ROOT . '/prompt.txt',
            'output_dir' => WWW . '/js/plugins',
        ];

        $config += $defaults;

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

        if (!is_file($this->config['prompt_file'])) {
            throw new \Exception("Prompt file does not exist");
        }
    }

    /**
     * Send a prompt to an AI and return a valid piece of source code
     *
     * @param $promptMessages array of OpenAI-API-spec messages
     */
    public function create()
    {
        if (empty($this->description)) {
            throw new \Exception("Error - no description has been provided for this script");
        }

        $api_key = getenv('OPENAI_API_KEY');

        $params = [
            'api_key' => $api_key,
        ];
        $client = $this->provider->client($params);

        Log::debug('Requesting code from OpenAI model:' . getenv('OPENAI_MODEL_PRIMARY'));

        $response = '';

        try {
            $params = [
                'model' => getenv('OPENAI_MODEL_PRIMARY'),
                'messages' => $this->getPromptMessages(),
                'stop' => $this->stop,
            ];

            Log::debug('Sending request to AI');
            Log::debug(json_encode($params));
            $result = json_decode($client->chat($params));

            $response = $result->choices[0]->message->content ?? '';
            file_put_contents(ROOT . "/api.log", date("Y-m-d H:i:s"). "\n\n" . $response . "\n\n===\n", FILE_APPEND);
            Log::debug('Response received from OpenAI');
            Log::debug(json_encode($result));

            if ($this->code = $this->validate($response)) {
                $this->deploy();
            }
            
        } catch (\Exception $e) {
            Log::debug("AI API ERROR: " . $e->getMessage(), 'error');
            throw $e;
        }

        $responseCharCount = strlen($response);
        $tokenUsage = $result->usage->totalTokens ?? 0;

        if (!empty($params['model'])) {
            Analytics::logApiUsage($params['model'], strlen($this->getFullPrompt()), $responseCharCount, $tokenUsage);
            Log::debug($tokenUsage . ' tokens used');
        }

        // try {
        //     return $this->getValidPluginCode($this->getClass());
        // } catch (\Exception $e) {
        //     return ['error' => $e->getMessage()];
        // }
    }

    public function getFullPrompt()
    {
        return preg_replace('/{{USER_PROMPT}}/i', $this->getUserPrompt(), $this->getPrompt());
    }

    public function getUserDir(): string
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

    public function getPrompt()
    {
        return file_get_contents($this->config['prompt_file']);
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
        if (empty($this->name)) {
            throw new \Exception("Error: No name has been provided for this script");
        }

        return Cleaner::removeWhitespace($this->name);
    }
}

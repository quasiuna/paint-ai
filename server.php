<?php

require 'bootstrap.php';

switch ($_GET['method'] ?? null) {
    case 'load':
        if (!empty($_GET['plugin'])) {
            $plugin = $_GET['plugin'];

            try {
                $code = getValidPluginCode($plugin);
                exit(json_encode(['tool' => $plugin, 'pluginCode' => $code]));
            } catch (\Exception $e) {
                exit(json_encode(['error' => $e->getMessage()]));
            }
        }
        break;
    case 'ai':
        if (!canAccessAPI()) {
            throw new \Exception("Rate Limit Exceeded");
        }
        $incoming_data = file_get_contents('php://input');
        Log::debug($incoming_data);
        $data = json_decode($incoming_data, true);

        if (!empty($data['tool']) && !empty($data['prompt'])) {
            $tool_name = trim(removeNewLinesFromString($data['tool']));
            $tool_class = removeWhitespace($tool_name);
            $tool_description = trim(removeNewLinesFromString($data['prompt']));
            $user_prompt = $tool_name . ': ' . $tool_description;
        } else {
            throw new \Exception("Invalid prompt");
        }

        $prompt = file_get_contents(ROOT . '/prompt.txt');
        $prompt = str_replace('{{USER_PROMPT}}', $user_prompt, $prompt);
        Log::debug($user_prompt);

        $api_key = getenv('OPENAI_API_KEY');
        $client = OpenAI::client($api_key);

        Log::debug('Requesting code from OpenAI model:' . getenv('OPENAI_MODEL_PRIMARY'));

        $response = '';

        try {
            $result = $client->chat()->create([
                'model' => getenv('OPENAI_MODEL_PRIMARY'),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ],
                ],
            ]);

            $response = $result->choices[0]->message->content ?? '';
            Log::debug('Response received from OpenAI');

            $response = trim(extractJs($response));
            $target_path = ROOT . '/js/plugins/' . $tool_class . '.js';
            if (is_file($target_path)) {
                $tool_class = $tool_class . '_' . time();
                $target_path = ROOT . '/js/plugins/' . $tool_class . '.js';
                Log::debug("Target tool path exists, renaming tool to [$tool_class]");
            }
            file_put_contents($target_path, $response);
            Log::debug(removeCommentsFromJavaScript($response));
        } catch (\Exception $e) {
            Log::debug("AI API ERROR: " . $e->getMessage(), 'error');
        }

        $responseCharCount = strlen($response);
        $tokenUsage = $result->usage->total_tokens ?? 0;

        Analytics::logApiUsage(strlen($prompt), $responseCharCount, $tokenUsage);

        Log::debug($tokenUsage . ' tokens used');

        try {
            $code = getValidPluginCode($tool_class);
            exit(json_encode(['tool' => $tool_class, 'pluginCode' => $code]));
        } catch (\Exception $e) {
            exit(json_encode(['error' => $e->getMessage()]));
        }
        break;
    case 'banter':
        $incoming_data = file_get_contents('php://input');
        Log::debug($incoming_data);
        $data = json_decode($incoming_data, true);
        if (!empty($data['tool'])) {
            $tool_name = trim(removeNewLinesFromString($data['tool']));
        } else {
            throw new \Exception("Invalid prompt");
        }
        $api_key = getenv('OPENAI_API_KEY');
        $client = OpenAI::client($api_key);

        Log::debug('Requesting banter from OpenAI');

        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => '(RULES: Respond only with the text of the joke, no explanation!) Tell me a joke about a feature for a Paint program where the user uses a "' . $tool_name . '" to create their artwork'
                ],
            ],
        ]);

        Log::debug('Response received from OpenAI');
        Log::debug(json_encode($result->usage ?? 'Unable to get token usage'));
        $response = $result->choices[0]->message->content ?? '';
        Log::debug($response);
        exit(json_encode(['banter' => $response]));
        break;
    default:
        exit('404 - Method not found');
        break;
}


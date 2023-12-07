<?php

require 'bootstrap.php';

switch ($_GET['method'] ?? null) {
    case 'load':
        if (!empty($_GET['plugin'])) {
            $plugin = $_GET['plugin'];

            if (preg_match('/^[a-z0-9]+$/i', $plugin)) {
                $path = ROOT . '/js/plugins/' . $plugin . '.js';
                if (is_file($path)) {
                    $pluginJs = file_get_contents($path);

                    if (validatePlugin($pluginJs)) {
                        exit(json_encode(['pluginCode' => $pluginJs]));
                    } else {
                        exit(json_encode(['error' => 'Plugin code did not pass server-side validation and will not be loaded']));
                    }
                } else {
                    throw new \Exception("Plugin does not exist");
                }
            } else {
                throw new \Exception("Plugin name invalid");
            }
        }
        break;
    case 'ai':
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

        Log::debug('Requesting code from OpenAI model:' . getenv('OPENAI_MODEL'));

        $result = $client->chat()->create([
            'model' => getenv('OPENAI_MODEL'),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ],
            ],
        ]);

        Log::debug('Response received from OpenAI');
        Log::debug(json_encode($result->usage ?? 'Unable to get token usage'));

        $response = $result->choices[0]->message->content ?? '';
        file_put_contents(ROOT . '/js/plugins/' . $tool_class . '.js', $response);
        Log::debug(removeCommentsFromJavaScript($response));
        echo $response;
        break;
    default:
        exit('404 - Method not found');
        break;
}


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
                        // dd($pluginJs, "\n\n\n\n******\n\n\n\n", cleanseJavaScript($pluginJs));
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
        dd($_REQUEST);
        $api_key = getenv('OPENAI_API_KEY');
        $client = OpenAI::client($api_key);

        $result = $client->chat()->create([
            'model' => getenv('OPENAI_MODEL'),
            'messages' => [
                ['role' => 'user', 'content' => 'Hello!'],
            ],
        ]);

        echo $result->choices[0]->message->content;
        break;
    default:
        exit('404 - Method not found');
        break;
}


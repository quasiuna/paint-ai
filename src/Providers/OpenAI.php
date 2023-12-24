<?php

namespace quasiuna\paintai\Providers;

use Orhanerday\OpenAi\OpenAi as OpenAiClient;

class OpenAI implements Provider
{
    public function client(array $config = []): OpenAiClient
    {
        return new OpenAiClient($config['api_key']);
    }
}

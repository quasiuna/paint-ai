<?php

namespace quasiuna\paintai\Providers;

use Orhanerday\OpenAi\OpenAi;
use quasiuna\paintai\Provider;

class LMStudio implements Provider
{
    public function client(array $config = []): OpenAi
    {
        $open_ai = new OpenAi('');
        $open_ai->setBaseURL("http://localhost:1234");

        return $open_ai;
    }
}

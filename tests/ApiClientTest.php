<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Orhanerday\OpenAi\OpenAi;

final class ApiClientTest extends TestCase
{
    public function setUp(): void
    {
        require_once __DIR__ . '/../bootstrap.php';
    }

    public function testConnectionOpenAi(): void
    {
        $api_key = getenv('OPENAI_API_KEY');
        $open_ai = new OpenAi($api_key);

        $models = $open_ai->listModels();

        $this->assertNotEmpty($models);

        $models = json_decode($models);
        
        $this->assertIsObject($models);
        $this->assertIsArray($models->data);
    }

    public function testConnectionLMStudio(): void
    {
        $open_ai = new OpenAi('');

        // ensure LM Studio is running on the following host for this test to work
        $open_ai->setBaseURL("http://localhost:1234");

        $chat = $open_ai->chat([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    "role" => "system",
                    "content" => "You create simple JavaScript classes, never commenting code and aiming for minimalism."
                ],
                [
                    "role" => "user",
                    "content" => "Create me a class for a 'Title' with which we can set its text and get its length. I'll tip you $200 if you Code Golf it!!!"
                ],
                [
                    "role" => "assistant",
                    "content" => "Here is my best code golf answer, JS with ZERO comments:\n```javascript\nclass Title"
                ],
            ],
            'stop' => [
                '```',
            ],
         ]);

        $response = json_decode($chat);
        $code = $response->choices[0]->message->content;

        $this->assertNotEmpty($code);
    }
}

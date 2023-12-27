<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use quasiuna\paintai\Plugin;
use quasiuna\paintai\Plugins\Text;
use quasiuna\paintai\Plugins\PaintTool;
use quasiuna\paintai\Providers\LMStudio;
use quasiuna\paintai\Providers\OpenAI;

final class PluginTest extends TestCase
{
    public $plugin = null;

    public function setUp(): void
    {
        require_once __DIR__ . '/../bootstrap.php';

        $this->plugin = new Plugin([
            'user' => 'testuser',
        ]);
    }

    public function testWriteFileToNewDir(): void
    {
        $path = '/tmp/a/b/c/d';
        $content = 'testcontent';

        $this->plugin->writeFile($path, $content);
        $this->assertFileExists($path);

        $fileContents = file_get_contents($path);
        $this->assertEquals($content, $fileContents);
    }

    public function testUser(): void
    {
        $this->assertEquals('testuser', $this->plugin->getUserDir());
    }

    public function testProviderOpenAI(): void
    {
        $provider = new OpenAI;

        $plugin = new PaintTool([
            'provider' => $provider,
            'name' => 'testname',
            'user' => 'testuser',
        ]);

        $client = $plugin->provider->client(['api_key' => 'abc123']);
        $this->assertInstanceOf('Orhanerday\OpenAi\OpenAi', $client);
    }

    public function testProviderLMStudio(): void
    {
        $plugin = new PaintTool([
            'provider' => new LMStudio,
            'name' => 'testname',
            'user' => 'testuser',
        ]);
        $client = $plugin->provider->client();

        $this->assertInstanceOf('Orhanerday\OpenAi\OpenAi', $client);
    }

    public function testProviderLMStudioRequest(): void
    {
        $provider = new LMStudio;

        $plugin = new Text([
            'provider' => $provider,
            'user' => 'testuser',
            'description' => 'testdescription',
        ]);

        $plugin->setMessages([
            [
                'role' => 'system',
                'content' => 'You are a geothermal engineer.',
            ],
            [
                'role' => 'user',
                'content' => 'tell me what you would like to do',
            ],
            [
                'role' => 'assistant',
                'content' => 'I would like you to dig a hole and then',
            ]
        ]);

        $plugin->create();
        $this->assertNotEmpty($plugin->code);
    }
}

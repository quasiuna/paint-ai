<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use quasiuna\paintai\AiScript;
use SebastianBergmann\Type\VoidType;

final class AiScriptTest extends TestCase
{
    public $ais = null;

    public function setUp(): void
    {
        require_once __DIR__ . '/../bootstrap.php';

        $params = [
            'name' => 'testname',
            'user' => 'testuser',
        ];
        $this->ais = new AiScript($params);
    }

    public function testWriteFileToNewDir(): void
    {
        $path = '/tmp/a/b/c/d';
        $content = 'testcontent';
        $this->ais->writeFile($path, 'testcontent');
        $this->assertFileExists($path);
        $fileContents = file_get_contents($path);
        $this->assertEquals($content, $fileContents);
    }

    public function testUser(): void
    {
        $this->assertEquals('testuser', $this->ais->getUserDir());
    }

    public function testPath(): void
    {
        $path = $this->ais->getPluginPath('testname');
        $path = str_replace(WWW, '', $path);
        $this->assertEquals('/js/plugins/testuser/testname.js', $path);
    }

    public function testExtractJsBackticks(): void
    {
        $input = '```javascript' . "\n";
        $input .= 'hello' . "\n";
        $input .= '```' . "\n";
        $this->assertEquals('hello', $this->ais->extractJs($input));
    }

    public function testExtractJsNoBackticks(): void
    {
        $input = 'hello' . "\n";
        $this->assertEquals('hello', $this->ais->extractJs($input));
    }

    public function testExtractJsStripeBeforePlugins(): void
    {
        $input = ' ' . "/* PLUGIN CODE HERE */\n\n" . ' ' . 'plugins.Example = ' . "\n";
        $this->assertEquals('plugins.Example =', $this->ais->extractJs($input));
    }

    public function testExtractJsStripeBeforePlugins2(): void
    {
        $input = 'const plugins.PenTool = ' . "\n";
        $this->assertEquals('plugins.PenTool =', $this->ais->extractJs($input));
    }

    public function testDeleteScript(): void
    {
        $unique_id = substr(md5((string) (microtime(true) * rand(1,1000))), 0, 4);
        $tool_name = 'TestTool' . $unique_id;
        $code = 'plugins.' . $tool_name . ' = class extends Tool {}';
        $this->ais->saveCode($code);
        $path = $this->ais->config['output_dir'] . '/testuser/' . $tool_name . '.js';
        $this->assertFileExists($path);
        $this->ais->delete($tool_name);
        $this->assertFileDoesNotExist($path);
        
        // clean up
        unlink($this->ais->getArchivePath($tool_name));
    }
}

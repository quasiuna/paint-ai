<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use quasiuna\paintai\Plugins\Plugin;
use quasiuna\paintai\Plugins\Text;
use quasiuna\paintai\Plugins\PaintTool;
use quasiuna\paintai\Providers\LMStudio;
use quasiuna\paintai\Providers\OpenAI;

final class PaintToolTest extends TestCase
{
    public $plugin = null;

    public function setUp(): void
    {
        require_once __DIR__ . '/../bootstrap.php';

        $this->plugin = new PaintTool([
            'user' => 'testuser',
            'name' => 'testname',
        ]);
    }

    public function testPath(): void
    {
        $path = $this->plugin->getPluginPath('testname');
        $path = str_replace(WWW, '', $path);
        $this->assertEquals('/js/plugins/testuser/testname.js', $path);
    }

    public function testExtractJsBackticks(): void
    {
        $input = '```javascript' . "\n";
        $input .= 'hello' . "\n";
        $input .= '```' . "\n";
        $this->assertEquals('hello', $this->plugin->extractJs($input));
    }

    public function testExtractJsNoBackticks(): void
    {
        $input = 'hello' . "\n";
        $this->assertEquals('hello', $this->plugin->extractJs($input));
    }

    public function testExtractJsStripeBeforePlugins(): void
    {
        $input = ' ' . "/* PLUGIN CODE HERE */\n\n" . ' ' . 'plugins.Example = ' . "\n";
        $this->assertEquals('plugins.Example =', $this->plugin->extractJs($input));
    }

    public function testExtractJsStripeBeforePlugins2(): void
    {
        $input = 'const plugins.PenTool = ' . "\n";
        $this->assertEquals('plugins.PenTool =', $this->plugin->extractJs($input));
    }

    public function testDeleteScript(): void
    {
        $unique_id = substr(md5((string) (microtime(true) * rand(1,1000))), 0, 4);
        $tool_name = 'TestTool' . $unique_id;
        $code = 'plugins.' . $tool_name . ' = class extends Tool {}';
        $this->plugin->saveCode($code);

        $path = $this->plugin->config['output_dir'] . '/testuser/' . $tool_name . '.js';
        $this->assertFileExists($path);
        $this->plugin->delete($tool_name);
        $this->assertFileDoesNotExist($path);
        
        // clean up
        unlink($this->plugin->getArchivePath($tool_name));
    }
}

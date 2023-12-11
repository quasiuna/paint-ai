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
}

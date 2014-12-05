<?php
namespace Janitor;

use Janitor\TestCases\JanitorTestCase;

class CodebaseTest extends JanitorTestCase
{
	public function testCanFindFilesInCodebase()
	{
		$files = $this->codebase->getFiles();
		$file  = $files[$this->appPath.'/src/SomeClass.php'];

		$this->assertInstanceOf('Symfony\Component\Finder\SplFileInfo', $file);
		$this->assertEquals('src/SomeClass.php', $file->getRelativePathname());
	}

	public function testCanTokenizePhpFiles()
	{
		$tokenized = $this->codebase->getTokenized();

		$this->assertEquals([19 => 'index'], $tokenized['SomeClass.php']);
	}

	public function testCanTokenizeJsonFiles()
	{
		$tokenized = $this->codebase->getTokenized();

		$this->assertEquals([5 => 'views', 17 => 'index'], $tokenized['something.json']);
	}

	public function testCanTokenizeBladeFiles()
	{
		$tokenized = $this->codebase->getTokenized();

		$this->assertEquals([
			5  => 'content',
			18 => 'some/partial',
			62 => 'app',
		], $tokenized['show.blade.php']);
	}

	public function testCanTokenizeXmlFiles()
	{
		$tokenized = $this->codebase->getTokenized();

		$this->assertContains('Janitor Test Suite', $tokenized['somefile.xml']);
	}

	public function testCanTokenizeYamlFiles()
	{
		$tokenized = $this->codebase->getTokenized();

		$this->assertContains('SomeClass@index', $tokenized['config.yml']);
	}

	public function testCanTokenizeTwigFiles()
	{
		$tokenized = $this->codebase->getTokenized();

		$this->assertEquals(['some.layout', 'some/partial.twig'], $tokenized['index.twig']);
	}
}

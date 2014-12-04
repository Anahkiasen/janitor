<?php
namespace Janitor;

use Janitor\TestCases\JanitorTestCase;

class CodebaseTest extends JanitorTestCase
{
	public function testCanFindFilesInCodebase()
	{
		$files    = $this->codebase->getFiles();
		$file     = head($files);

		$this->assertInstanceOf('Symfony\Component\Finder\SplFileInfo', $file);
		$this->assertEquals('src/SomeClass.php', $file->getRelativePathname());
	}

	public function testCanTokenizePhpFiles()
	{
		$tokenized = $this->codebase->getTokenized();

		$this->assertEquals([19 => 'index'], $tokenized['SomeClass.php']);
	}

	public function testCanTokenizeTwigFiles()
	{
		$tokenized = $this->codebase->getTokenized();

		$this->assertEquals(['some.layout', 'some/partial.twig'], $tokenized['index.twig']);
	}
}

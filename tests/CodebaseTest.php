<?php
namespace Janitor;

use Janitor\TestCases\JanitorTestCase;

class CodebaseTest extends JanitorTestCase
{
	public function testCanFindFilesInCodebase()
	{
		$codebase = $this->getCodebase();
		$files    = $codebase->getFiles();
		$file     = head($files);

		$this->assertInstanceOf('Symfony\Component\Finder\SplFileInfo', $file);
		$this->assertEquals('src/SomeClass.php', $file->getRelativePathname());
	}

	public function testCanTokenizePhpFiles()
	{
		$codebase = $this->getCodebase();
		$tokenized = $codebase->getTokenized();

		$this->assertEquals([19 => 'some-view'], $tokenized['SomeClass.php']);
	}

	public function testCanTokenizeTwigFiles()
	{
		$codebase = $this->getCodebase();
		$tokenized = $codebase->getTokenized();

		$this->assertEquals(['some.layout', 'some/partial.twig'], $tokenized['index.twig']);
	}

	/**
	 * @return Codebase
	 */
	protected function getCodebase()
	{
		return new Codebase(__DIR__.'/_application');
	}
}

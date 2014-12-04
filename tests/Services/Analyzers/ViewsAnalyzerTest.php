<?php
namespace Janitor\Services\Analyzers;

use Janitor\TestCases\JanitorTestCase;

class ViewsAnalyzerTest extends JanitorTestCase
{
	public function testThrowsExceptionIfNoSetFiles()
	{
		$this->setExpectedException('Janitor\Exceptions\UndefinedSubjectException');

		$analyzer = new ViewsAnalyzer($this->codebase);
		$analyzer->analyze();
	}

	public function testCanAnalyzeViewsUsage()
	{
		$analyzer = new ViewsAnalyzer($this->codebase);
		$analyzer->setFiles($this->appPath.'/views', 'twig');
		$results = $analyzer->analyze();

		$this->assertInstanceOf('Janitor\Entities\View', $results->first());
		$this->assertEquals(['index.twig' => 0.25, 'unused.twig' => 0], $results->lists('usage', 'name'));
	}
}

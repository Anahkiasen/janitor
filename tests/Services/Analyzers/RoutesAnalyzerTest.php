<?php
namespace Janitor\Services\Analyzers;

use Janitor\TestCases\JanitorTestCase;
use Mockery;

class RoutesAnalyzerTest extends JanitorTestCase
{
	public function testThrowsExceptionIfNoSetFiles()
	{
		$this->setExpectedException('Janitor\Exceptions\UndefinedSubjectException');

		$analyzer = new RoutesAnalyzer($this->codebase);
		$analyzer->analyze();
	}

	public function testCanAnalyzeRoutesUsage()
	{
		$analyzer = new RoutesAnalyzer($this->codebase);
		$analyzer->setRoutes(array(
			$this->mockRoute('/', 'SomeClass@index'),
			$this->mockRoute('/unused', 'SomeClass@unused'),
		));

		$results = $analyzer->analyze();
		$this->assertInstanceOf('Janitor\Entities\Route', $results->first());
		$this->assertEquals(['GET /' => 1, 'GET /unused' => 0], $results->lists('usage', 'name'));
	}
}

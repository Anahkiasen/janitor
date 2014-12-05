<?php
namespace Janitor\Abstracts;

use Illuminate\Support\Collection;
use Janitor\Services\Analyzers\ViewsAnalyzer;
use Janitor\TestCases\JanitorTestCase;
use Mockery;

class AbstractAnalyzerTest extends JanitorTestCase
{
	public function testCanGetOnlyUnusedEntities()
	{
		$analyzer = new ViewsAnalyzer($this->codebase);
		$analyzer->setEntities(new Collection(array(
			$this->mockAbstractEntity(1),
			$this->mockAbstractEntity(0),
		)));

		$results = $analyzer->getUnusedEntities();
		$this->assertCount(1, $results);
		$this->assertEquals(0, $results->first()->usage);
	}

	/**
	 * @param integer $usage
	 *
	 * @return Mockery\MockInterface
	 */
	protected function mockAbstractEntity($usage)
	{
		$mocked = Mockery::mock('Janitor\Abstracts\AbstractAnalyzedEntity', ['name' => 'foo']);
		$mocked->usage = $usage;

		return $mocked;
	}
}

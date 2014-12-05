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
			$this->mockAbstractEntity(0.5),
			$this->mockAbstractEntity(0.25),
			$this->mockAbstractEntity(0),
		)));

		$results = $analyzer->getUnusedEntities(0.25)->all();
		$results = array_values($results);

		$this->assertCount(2, $results);
		$this->assertEquals(0, $results[1]->usage);
		$this->assertEquals(0.25, $results[0]->usage);
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

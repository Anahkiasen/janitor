<?php
namespace Janitor\Commands;

use Janitor\TestCases\JanitorTestCase;
use Mockery;

class CleanRoutesTest extends JanitorTestCase
{
	public function testCanFindUnusedRoutes()
	{
		$this->app['router'] = Mockery::mock('Router', array(
			'getRoutes' => array(
				$this->mockRoute('/', 'SomeClass@index'),
				$this->mockRoute('/unused', 'SomeClass@unused'),
			),
		));

		$tester = $this->testCommand('Janitor\Commands\CleanRoutes');

		$this->assertContains('unused routes were found', $tester->getDisplay());
	}
}

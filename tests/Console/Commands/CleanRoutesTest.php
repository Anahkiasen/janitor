<?php
namespace Janitor\Console\Commands;

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

		$tester = $this->getCommandTester('Janitor\Console\Commands\CleanRoutes');

		$this->assertContains('unused routes were found', $tester->getDisplay());
	}
}

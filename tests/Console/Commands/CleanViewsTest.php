<?php
namespace Janitor\Console\Commands;

use Janitor\TestCases\JanitorTestCase;

class CleanViewsTest extends JanitorTestCase
{
	public function testCanFindUnusedViews()
	{
		$tester = $this->getCommandTester('Janitor\Console\Commands\CleanViews');

		$this->assertContains('unused entities were found', $tester->getDisplay());
	}
}

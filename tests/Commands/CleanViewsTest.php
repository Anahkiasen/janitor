<?php
namespace Janitor\Commands;

use Janitor\TestCases\JanitorTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CleanViewsTest extends JanitorTestCase
{
	public function testCanFindUnusedViews()
	{
		$tester = $this->testCommand('Janitor\Commands\CleanViews');

		$this->assertContains('unused views were found', $tester->getDisplay());
	}
}

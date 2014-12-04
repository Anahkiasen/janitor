<?php
namespace Janitor;

use Janitor\TestCases\JanitorTestCase;

class UsageNeedleTest extends JanitorTestCase
{
	public function testCanMatchStringAgainstTokens()
	{
		$usageNeedle = new UsageNeedle(1, ['foo', 'bar']);

		$this->assertTrue($usageNeedle->matches('foobaz'));
		$this->assertFalse($usageNeedle->matches('bazqux'));
	}

	public function testCanMatchRegexAgainstTokens()
	{
		$usageNeedle = new UsageNeedle(1, ['/f[a-z]{2}/'], true);

		$this->assertTrue($usageNeedle->matches('foobaz'));
		$this->assertFalse($usageNeedle->matches('bazqux'));
	}
}

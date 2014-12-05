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
		$usageNeedle = new UsageNeedle(1, '/f[a-z]{2}/');
		$usageNeedle->setRegex(true);

		$this->assertTrue($usageNeedle->matches('foobaz'));
		$this->assertFalse($usageNeedle->matches('bazqux'));
	}

	public function testCanFilterEmptyNeedles()
	{
		$usageNeedle = new UsageNeedle(1, '');
		$usageNeedle->matches('foobar');
	}

	public function testCanLooselyMatchContent()
	{
		$usageNeedle = new UsageNeedle(1, 'URL::action("Foo@bar")');
		$usageNeedle->setLoose(true);

		$this->assertTrue($usageNeedle->matches("URL.action('Foo@bar')"));
		$this->assertTrue($usageNeedle->matches('$url->action("Foo@bar")'));
		$this->assertFalse($usageNeedle->matches('URL::action("Bar@baz")'));
	}

	public function testLooseMatchAffectsUsageCertainty()
	{
		$usageNeedle = new UsageNeedle(1, 'URL::action("Foo@bar")');
		$usageNeedle->setLoose(true);
		$usageNeedle->matches('URL::action("Foo@bar")');
		$this->assertEquals(1, $usageNeedle->usage);

		$usageNeedle = new UsageNeedle(1, 'URL::action("Foo@bar")');
		$usageNeedle->setLoose(true);
		$usageNeedle->matches('URL.action("Foo@bar")');
		$this->assertEquals(0.8, $usageNeedle->usage);
	}

	public function testDoesntCrashOnLooseMatchOfLongerContent()
	{
		$usageNeedle = new UsageNeedle(1, 'URL::action("Foo@bar")');
		$usageNeedle->setLoose(true);
		$usageNeedle->matches(str_repeat('foo', 255));
	}
}

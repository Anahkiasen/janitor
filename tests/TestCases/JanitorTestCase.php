<?php
namespace Janitor\TestCases;

use Illuminate\Container\Container;
use Janitor\JanitorServiceProvider;
use PHPUnit_Framework_TestCase;

class JanitorTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * @type Container
	 */
	protected $app;

	/**
	 * Setup the tests
	 */
	public function setUp()
	{
		$this->app = new Container();
		$provider  = new JanitorServiceProvider($this->app);
		$provider->register();
		$provider->boot();
	}
}

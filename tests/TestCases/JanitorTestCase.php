<?php
namespace Janitor\TestCases;

use Illuminate\Container\Container;
use Janitor\Codebase;
use Janitor\JanitorServiceProvider;
use PHPUnit_Framework_TestCase;

abstract class JanitorTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * @type Container
	 */
	protected $app;

	/**
	 * @type Codebase
	 */
	protected $codebase;

	/**
	 * Path to the dummy application
	 *
	 * @type string
	 */
	protected $appPath;

	/**
	 * Setup the tests
	 */
	public function setUp()
	{
		$this->app = new Container();
		$provider  = new JanitorServiceProvider($this->app);
		$provider->register();
		$provider->boot();

		$this->appPath  = realpath(__DIR__.'/../_application');
		$this->codebase = new Codebase($this->appPath);
	}
}

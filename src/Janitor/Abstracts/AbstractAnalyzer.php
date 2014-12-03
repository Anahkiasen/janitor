<?php
namespace Janitor\Abstracts;

use Illuminate\Container\Container;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractAnalyzer
{
	/**
	 * @type Container
	 */
	protected $app;
	/**
	 * @type OutputInterface
	 */
	protected $output;

	/**
	 * @param Container $app
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	//////////////////////////////////////////////////////////////////////
	/////////////////////////////// OUTPUT ///////////////////////////////
	//////////////////////////////////////////////////////////////////////

	/**
	 * @param string $message
	 */
	public function line($message)
	{
		if (!$this->output) {
			return;
		}

		$this->output->writeln('<comment>'.$message.'</comment>');
	}

	/**
	 * @param OutputInterface $output
	 */
	public function setOutput(OutputInterface $output)
	{
		$this->output = $output;
	}
}

<?php
namespace Janitor\Abstracts;

use Illuminate\Support\Collection;
use Janitor\Models\Codebase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

abstract class AbstractAnalyzer
{
	/**
	 * @type Codebase
	 */
	protected $codebase;

	/**
	 * The existing views
	 *
	 * @type Collection
	 */
	protected $files;

	/**
	 * @type OutputInterface
	 */
	protected $output;

	/**
	 * @param Codebase $codebase
	 */
	public function __construct(Codebase $codebase)
	{
		$this->codebase = $codebase;
	}

	//////////////////////////////////////////////////////////////////////
	/////////////////////////////// FILES ////////////////////////////////
	//////////////////////////////////////////////////////////////////////

	/**
	 * Setup the files to analyze
	 *
	 * @param string $folder
	 * @param string $extensions
	 */
	public function setFiles($folder, $extensions)
	{
		// Create Finder
		$finder     = new Finder();
		$extensions = implode('|', $extensions);
		$files      = $finder->files()->in($folder)->name('/\.('.$extensions.')/');
		$files      = iterator_to_array($files);

		$this->files = new Collection($files);
	}

	/**
	 * @return Collection
	 */
	public function getFiles()
	{
		return $this->files;
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

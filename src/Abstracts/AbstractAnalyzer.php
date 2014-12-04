<?php
namespace Janitor\Abstracts;

use Illuminate\Support\Collection;
use Janitor\Entities\AnalyzedFile;
use Janitor\Entities\Codebase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

abstract class AbstractAnalyzer
{
	/**
	 * @type \Janitor\Entities\Codebase
	 */
	protected $codebase;

	/**
	 * The existing views
	 *
	 * @type Collection|AnalyzedFile[]
	 */
	protected $files;

	/**
	 * @type OutputInterface
	 */
	protected $output;

	/**
	 * @param \Janitor\Entities\Codebase $codebase
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
		$finder     = $finder->files()->in($folder)->name('/\.('.$extensions.')/');

		// Set ignored patterns
		foreach ($this->codebase->getIgnored() as $pattern) {
			$finder = $finder->notPath($pattern)->notName($pattern);
		}

		// Wrap into Collection
		$files = iterator_to_array($finder);
		$files = new Collection($files);

		$this->files = $files;
	}

	/**
	 * @return Collection|AnalyzedFile[]
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

	//////////////////////////////////////////////////////////////////////
	////////////////////////////// ANALYZE ///////////////////////////////
	//////////////////////////////////////////////////////////////////////

	/**
	 * Check if multiple string appear in an array
	 *
	 * @param array $tokens
	 * @param array $needles
	 *
	 * @return boolean
	 */
	protected function containsTokens(array $tokens, array $needles)
	{
		foreach ($tokens as $token) {
			foreach ($needles as $needle) {
				if ($needle != '' && strpos($token, $needle) !== false) {
					return true;
				}
			}
		}

		return false;
	}
}

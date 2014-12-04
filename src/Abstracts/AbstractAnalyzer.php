<?php
namespace Janitor\Abstracts;

use Illuminate\Support\Collection;
use Janitor\Codebase;
use Janitor\UsageNeedle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

abstract class AbstractAnalyzer
{
	/**
	 * @type \Janitor\Codebase
	 */
	protected $codebase;

	/**
	 * The existing views
	 *
	 * @type Collection|AbstractAnalyzedEntity[]
	 */
	protected $files;

	/**
	 * @type OutputInterface
	 */
	protected $output;

	/**
	 * @param \Janitor\Codebase $codebase
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
	 * Get all analyzed files
	 *
	 * @return Collection|AbstractAnalyzedEntity[]
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * Get analyzed files unused by a certain threshold
	 *
	 * @param integer $threshold
	 *
	 * @return Collection|AbstractAnalyzedEntity[]
	 */
	public function getUnusedFiles($threshold = 0)
	{
		$files = clone $this->files;
		$files = $files->filter(function (AbstractAnalyzedEntity $file) use ($threshold) {
			return $file->usage <= $threshold;
		});

		return $files;
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
	 * @param array       $tokens
	 * @param UsageNeedle $usageNeedle
	 *
	 * @return boolean
	 */
	protected function containsTokens(array $tokens, UsageNeedle $usageNeedle)
	{
		foreach ($tokens as $token) {
			foreach ($usageNeedle->needles as $needle) {
				if ($needle != '' && strpos($token, $needle) !== false) {
					return true;
				}
			}
		}

		return false;
	}
}

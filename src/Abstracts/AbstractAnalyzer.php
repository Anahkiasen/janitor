<?php
namespace Janitor\Abstracts;

use Illuminate\Support\Collection;
use Janitor\Codebase;
use Janitor\UsageNeedle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Janitor\Abstracts\AbstractAnalyzedEntity;

abstract class AbstractAnalyzer
{
	/**
	 * @type \Janitor\Codebase
	 */
	protected $codebase;

	/**
	 * The folder of files to analyze
	 *
	 * @type string
	 */
	protected $folder;

	/**
	 * The files containing the entities
	 *
	 * @type Collection|AbstractAnalyzedEntity[]
	 */
	protected $files;

	/**
	 * The entities being analyzed
	 *
	 * @type Collection|AbstractAnalyzedEntity[]
	 */
	protected $entities;

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

	/**
	 * @return Collection|AbstractAnalyzedEntity[]
	 */
	public function getEntities()
	{
		return $this->entities;
	}

	/**
	 * Get analyzed entities unused by a certain threshold
	 *
	 * @param integer $threshold
	 *
	 * @return Collection|AbstractAnalyzedEntity[]
	 */
	public function getUnusedEntities($threshold = 0)
	{
		$entities = clone $this->entities;
		$entities = $entities->filter(function (AbstractAnalyzedEntity $entity) use ($threshold) {
			return $entity->usage <= $threshold;
		});

		return $entities;
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

		$this->folder = $folder;
		$this->files  = $files;
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
	 * Compute the entities from the information
	 * that was passed to the analyzed
	 *
	 * @return AbstractAnalyzedEntity[]
	 */
	abstract protected function createEntities();

	/**
	 * Analyze the entities and compute their usage
	 *
	 * @return Collection
	 */
	public function analyze()
	{
		$this->entities = new Collection($this->createEntities());
		$codebase       = $this->codebase->getTokenized();

		/** @type AbstractAnalyzedEntity $entity */
		foreach ($this->entities as $key => $entity) {
			foreach ($codebase as $tokens) {
				if (!$tokens) {
					continue;
				}

				foreach ($entity->getUsageMatrix() as $usageNeedle) {
					if ($this->containsTokens($tokens, $usageNeedle)) {
						$this->entities[$key]->usage = $usageNeedle->usage;
						break 2;
					}
				}
			}
		}

		return $this->entities;
	}

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

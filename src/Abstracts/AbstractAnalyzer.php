<?php
namespace Janitor\Abstracts;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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

	//////////////////////////////////////////////////////////////////////
	////////////////////////////// ENTITIES //////////////////////////////
	//////////////////////////////////////////////////////////////////////

	/**
	 * Compute the entities from the information
	 * that was passed to the analyzer
	 *
	 * @return AbstractAnalyzedEntity[]
	 */
	abstract protected function createEntities();

	/**
	 * Filter out ignored entities
	 *
	 * @return Collection
	 */
	protected function filterEntities()
	{
		$ignored = $this->codebase->getIgnored();

		$entities = new Collection($this->createEntities());
		$entities = $entities->filter(function (AbstractAnalyzedEntity $entity) use ($ignored) {
			return !Str::contains($entity->name, $ignored);
		});

		return $entities;
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
	 * @param string          $folder
	 * @param string|string[] $extensions
	 */
	public function setFiles($folder, $extensions)
	{
		// Create Finder
		$finder     = new Finder();
		$extensions = (array) $extensions;
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
	 * Analyze the entities and compute their usage
	 *
	 * @return Collection
	 */
	public function analyze()
	{
		$this->entities = $this->filterEntities();
		$codebase       = $this->codebase->getTokenized();

		/** @type AbstractAnalyzedEntity $entity */
		foreach ($this->entities as $key => $entity) {
			foreach ($codebase as $file => $tokens) {
				if (!$tokens) {
					continue;
				}

				foreach ($entity->getUsageMatrix() as $usageNeedle) {
					if ($token = $this->containsTokens($tokens, $usageNeedle)) {
						$this->entities[$key]->usage        = $usageNeedle->usage;
						$this->entities[$key]->occurences[] = array(
							'file'    => $file,
							'context' => $token,
						);

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
	 * @return string|false
	 */
	protected function containsTokens(array $tokens, UsageNeedle $usageNeedle)
	{
		foreach ($tokens as $token) {
			if ($usageNeedle->matches($token)) {
				return $token;
			}
		}

		return false;
	}
}

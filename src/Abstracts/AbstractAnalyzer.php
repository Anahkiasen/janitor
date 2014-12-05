<?php
namespace Janitor\Abstracts;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Janitor\Codebase;
use Janitor\UsageNeedle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;
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
	 * @param Collection|AbstractAnalyzedEntity[] $entities
	 */
	public function setEntities(Collection $entities)
	{
		$this->entities = $entities;
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
		return $this->entities->filter(function (AbstractAnalyzedEntity $entity) use ($threshold) {
			return $entity->usage <= $threshold;
		});
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

	/**
	 * @param array $entries
	 *
	 * @return ProgressBar
	 */
	protected function getProgressBar(array $entries)
	{
		$progress = new ProgressBar($this->output ?: new NullOutput(), sizeof($entries));
		$progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% <info>%message%</info>');
		$progress->start();

		return $progress;
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
		$this->line('Tokenizing your codebase, this can take a few moments');
		$this->entities = $this->filterEntities();
		$codebase       = $this->codebase->getTokenized();

		/** @type AbstractAnalyzedEntity $entity */
		$this->line('Analyzing codebase...');
		$progress = $this->getProgressBar($this->entities);

		foreach ($this->entities as $key => $entity) {
			$progress->advance();
			$progress->setMessage($entity->name);

			foreach ($entity->getUsageMatrix() as $usageNeedle) {
				foreach ($codebase as $file => $tokens) {
					if (!$tokens) {
						continue;
					}

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

		$progress->finish();

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

<?php
namespace Janitor\Commands;

use Illuminate\Console\Command;
use Janitor\Services\Analyzers\RoutesAnalyzer;

class CleanRoutes extends Command
{
	/**
	 * @type string
	 */
	protected $name = 'janitor:routes';

	/**
	 * @type string
	 */
	protected $description = 'Look for unused routes';

	/**
	 * @type RoutesAnalyzer
	 */
	protected $analyzer;

	/**
	 * @param RoutesAnalyzer $analyzer
	 */
	public function __construct(RoutesAnalyzer $analyzer)
	{
		parent::__construct();

		$this->analyzer = $analyzer;
	}

	/**
	 * Execute the command
	 */
	public function fire()
	{
		$this->analyzer->setOutput($this->output);
		$this->analyzer->analyze();

		$unused = $this->analyzer->getUnusedEntities();
		$this->comment($unused->count().' unused routes were found:');
		foreach ($unused as $entity) {
			$this->line('| '.$entity->name);
		}
	}
}

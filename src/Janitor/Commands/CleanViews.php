<?php
namespace Janitor\Commands;

use Illuminate\Console\Command;
use Janitor\Models\View;
use Janitor\Analyzers\ViewsAnalyzer;
use Symfony\Component\Console\Helper\Table;

class CleanViews extends Command
{
	/**
	 * @type string
	 */
	protected $name = 'janitor:views';

	/**
	 * @type ViewsAnalyzer
	 */
	protected $cleaner;

	/**
	 * @param ViewsAnalyzer $cleaner
	 */
	public function __construct(ViewsAnalyzer $cleaner)
	{
		parent::__construct();

		$this->cleaner = $cleaner;
	}

	/**
	 * Execute the command
	 */
	public function fire()
	{
		$this->cleaner->setOutput($this->output);
		$this->cleaner->analyze();
		$unused = $this->cleaner->getViews();
		$unused = $unused->sortBy('usage');
		$unused = $unused->map(function (View $view) {
			return [$view->name, $view->usage * 100];
		});

		$table = new Table($this->output);
		$table->setHeaders(['View', 'Usage certainty']);
		$table->setRows($unused->all());
		$table->render();
	}
}

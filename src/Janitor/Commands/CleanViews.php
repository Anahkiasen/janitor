<?php
namespace Janitor\Commands;

use Illuminate\Console\Command;
use Janitor\Services\Analyzers\ViewsAnalyzer;
use Janitor\Services\Entities\View;
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
	protected $analyzer;

	/**
	 * @param ViewsAnalyzer $analyzer
	 */
	public function __construct(ViewsAnalyzer $analyzer)
	{
		parent::__construct();

		$this->analyzer = $analyzer;
	}

	/**
	 * Execute the command
	 */
	public function fire()
	{
		$views = $this->laravel['config']['view.paths'][0];

		// Setup analyzer
		$this->analyzer->setOutput($this->output);
		$this->analyzer->setFiles($views, ['php', 'twig']);
		$this->analyzer->analyze();

		// Get unused views
		$unused = $this->analyzer->getFiles();
		$unused = $unused->sortBy('usage');
		$unused = $unused->map(function (View $view) {
			return [$view->name, $view->usage * 100];
		});

		// Present views
		$table = new Table($this->output);
		$table->setHeaders(['View', 'Usage certainty']);
		$table->setRows($unused->all());
		$table->render();
	}
}

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
		$unused = $unused->filter(function ($view) {
			return $view->usage === 0;
		});

		$this->comment($unused->count(). ' unused views were found:');
		foreach ($unused as $view) {
			$this->line('| '.$view->name);
		}
	}
}

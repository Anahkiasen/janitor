<?php
namespace Janitor\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Janitor\Entities\View;
use Janitor\Services\Analyzers\ViewsAnalyzer;
use Symfony\Component\Console\Input\InputOption;

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
	 * @type Filesystem
	 */
	private $files;

	/**
	 * @param ViewsAnalyzer $analyzer
	 */
	public function __construct(ViewsAnalyzer $analyzer, Filesystem $files)
	{
		parent::__construct();

		$this->analyzer = $analyzer;
		$this->files    = $files;
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

		// Display unused views
		$this->comment($unused->count().' unused views were found:');
		foreach ($unused as $view) {
			$this->line('| '.$view->name);
		}

		// Remove if asked
		if ($this->option('delete')) {
			$unused = $unused->map(function (View $view) {
				return realpath($view->file->getPathname());
			});

			$this->files->delete($unused->all());
		}
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return array(
			['delete', null, InputOption::VALUE_NONE, 'Delete the unused views found'],
		);
	}
}

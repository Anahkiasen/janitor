<?php
namespace Janitor\Console\Commands;

use Illuminate\Filesystem\Filesystem;
use Janitor\Abstracts\Console\AbstractAnalyzerCommand;
use Janitor\Entities\View;
use Janitor\Services\Analyzers\ViewsAnalyzer;
use Symfony\Component\Console\Input\InputOption;

class CleanViews extends AbstractAnalyzerCommand
{
	/**
	 * @type string
	 */
	protected $name = 'janitor:views';

	/**
	 * @type string
	 */
	protected $description = 'Look for unused views';

	/**
	 * @type Filesystem
	 */
	protected $files;

	/**
	 * @param ViewsAnalyzer $analyzer
	 * @param Filesystem    $files
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
		// Configure Analyzer
		$views = $this->laravel['config']['view.paths'][0];
		$this->analyzer->setFiles($views, ['php', 'twig']);

		parent::fire();

		// Remove if asked
		if ($this->option('delete')) {
			$unused = $this->results->map(function (View $view) {
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
		return array_merge(parent::getOptions(), array(
			['delete', null, InputOption::VALUE_NONE, 'Delete the unused views found'],
		));
	}
}

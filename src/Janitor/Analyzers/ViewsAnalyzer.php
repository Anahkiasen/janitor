<?php
namespace Janitor\Analyzers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Janitor\Abstracts\AbstractAnalyzer;
use Janitor\Interfaces\AnalyzerInterface;
use Janitor\Models\View;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ViewsAnalyzer extends AbstractAnalyzer implements AnalyzerInterface
{
	/**
	 * The existing views
	 *
	 * @type View[]|Collection
	 */
	protected $views;

	/**
	 * @return View[]|Collection
	 */
	public function getViews()
	{
		return $this->views;
	}

	/**
	 * Clean the views
	 */
	public function analyze()
	{
		$this->views = $this->gatherViews();

		$this->findUsages();
	}

	//////////////////////////////////////////////////////////////////////
	////////////////////////////// ANALYZE ///////////////////////////////
	//////////////////////////////////////////////////////////////////////

	/**
	 * Get all the existing views
	 *
	 * @return Collection|View[]
	 */
	protected function gatherViews()
	{
		$viewsFolder = $this->app->config->get('view.paths')[0];

		// Create Finder
		$finder = new Finder();
		$views  = $finder->files()->in($viewsFolder);

		// Create View instances
		$collection = new Collection();
		foreach ($views as $view) {
			$collection[] = new View(array(
				'file'  => $view,
				'usage' => 0,
				'views' => $viewsFolder,
			));
		}

		return $collection;
	}

	/**
	 * Find usages of the views in the app's files
	 */
	protected function findUsages()
	{
		$codebase = $this->app['janitor.codebase']->getSerialized();

		foreach ($this->views as $key => $view) {
			foreach ($codebase as $file) {
				foreach ($view->getUsageNeedles() as $needle) {
					extract($needle);

					if (Str::contains($file, $needles)) {
						$this->views[$key]['usage'] = $usage;
						break 2;
					} else {
					}
				}
			}
		}
	}
}

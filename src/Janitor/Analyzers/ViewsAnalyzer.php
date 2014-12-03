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
	 * Setup the files to analyze
	 *
	 * @param string $folder
	 * @param string $extensions
	 */
	public function setFiles($folder, $extensions)
	{
		parent::setFiles($folder, $extensions);

		// Create View instances
		foreach ($this->files as $key => $view) {
			$this->files[$key] = new View(array(
				'file'  => $view,
				'usage' => 0,
				'views' => $folder,
			));
		}
	}

	/**
	 * Clean the views
	 */
	public function analyze()
	{
		$codebase = $this->codebase->getSerialized();

		foreach ($this->files as $key => $view) {
			foreach ($codebase as $file) {
				foreach ($view->getUsageNeedles() as $needle) {
					if (Str::contains($file, $needle['needles'])) {
						$this->files[$key]['usage'] = $needle['usage'];
						break 2;
					}
				}
			}
		}
	}
}

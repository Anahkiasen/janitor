<?php
namespace Janitor\Services\Analyzers;

use Janitor\Abstracts\AbstractAnalyzer;
use Janitor\Entities\View;
use Janitor\Interfaces\AnalyzerInterface;

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
		foreach ($this->files as $key => $file) {
			$view = new View($folder, $file->getPathname());
			$view->setFile($file);

			$this->files[$key] = $view;
		}
	}

	/**
	 * Clean the views
	 *
	 * @return Collection
	 */
	public function analyze()
	{
		$codebase = $this->codebase->getTokenized();

		/** @type \Janitor\Entities\View $view */
		foreach ($this->files as $key => $view) {
			foreach ($codebase as $tokens) {
				if (!$tokens) {
					continue;
				}

				foreach ($view->getUsageMatrix() as $usageNeedle) {
					if ($this->containsTokens($tokens, $usageNeedle)) {
						$this->files[$key]->usage = $usageNeedle->usage;
						break 2;
					}
				}
			}
		}

		return $this->getFiles();
	}
}

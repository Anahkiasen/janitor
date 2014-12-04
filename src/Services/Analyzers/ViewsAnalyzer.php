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
		foreach ($this->files as $key => $view) {
			$this->files[$key] = new View($view, $folder);
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

		foreach ($this->files as $key => $view) {
			foreach ($codebase as $tokens) {
				if (!$tokens) {
					continue;
				}

				foreach ($view->getUsageNeedles() as $needle) {
					if ($this->containsTokens($tokens, $needle['needles'])) {
						$this->files[$key]->usage = $needle['usage'];
						break 2;
					}
				}
			}
		}

		return $this->getFiles();
	}
}

<?php
namespace Janitor\Services\Analyzers;

use Janitor\Abstracts\AbstractAnalyzer;
use Janitor\Entities\View;
use Janitor\Interfaces\AnalyzerInterface;

class ViewsAnalyzer extends AbstractAnalyzer implements AnalyzerInterface
{
	/**
	 * Compute the entities from the information
	 * that was passed to the analyzer
	 *
	 * @return \Janitor\Abstracts\AbstractAnalyzedEntity[]
	 */
	protected function createEntities()
	{
		$entities = [];
		foreach ($this->files as $key => $file) {
			$view = new View($this->folder, $file->getPathname());
			$view->setFile($file);

			$entities[$key] = $view;
		}

		return $entities;
	}
}

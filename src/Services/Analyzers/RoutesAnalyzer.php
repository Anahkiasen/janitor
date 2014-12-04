<?php
namespace Janitor\Services\Analyzers;

use Janitor\Abstracts\AbstractAnalyzer;
use Janitor\Interfaces\AnalyzerInterface;

class RoutesAnalyzer extends AbstractAnalyzer implements AnalyzerInterface
{
	/**
	 * Run the analyze process
	 */
	public function analyze()
	{
		$routes   = $this->codebase->getRoutes();
		$codebase = $this->codebase->getTokenized();

		foreach ($routes as $route) {
			foreach ($codebase as $tokens) {
				// ...
			}
		}
	}
}

<?php
namespace Janitor\Services\Analyzers;

use Janitor\Abstracts\AbstractAnalyzer;
use Janitor\Entities\Route;
use Janitor\Interfaces\AnalyzerInterface;

class RoutesAnalyzer extends AbstractAnalyzer implements AnalyzerInterface
{
	/**
	 * Compute the entities from the information
	 * that was passed to the analyzed
	 *
	 * @return \Janitor\Abstracts\AbstractAnalyzedEntity[]
	 */
	protected function createEntities()
	{
		$entities         = [];
		$routesCollection = $this->codebase->getRoutes();

		/** @type \Illuminate\Routing\Route $route */
		foreach ($routesCollection as $route) {
			$name   = $route->getMethods()[0].' '.$route->getUri();
			$entity = new Route($this->folder, $name);
			$entity->setRoute($route);

			$entities[] = $entity;
		}

		return $entities;
	}
}

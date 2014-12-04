<?php
namespace Janitor\Entities;

use Illuminate\Http\Request;
use Illuminate\Routing\Route as RouteInstance;
use Janitor\Abstracts\AbstractAnalyzedEntity;
use Janitor\UsageNeedle;

class Route extends AbstractAnalyzedEntity
{
	/**
	 * @type RouteInstance
	 */
	protected $route;

	//////////////////////////////////////////////////////////////////////
	/////////////////////////////// ROUTE ////////////////////////////////
	//////////////////////////////////////////////////////////////////////

	/**
	 * @return RouteInstance
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * @param RouteInstance $route
	 */
	public function setRoute(RouteInstance $route)
	{
		// Compile route
		$request = new Request();
		$route->matches($request);

		$this->route = $route;
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////// USAGE MATRIX ////////////////////////////
	//////////////////////////////////////////////////////////////////////

	/**
	 * Compute the usage matrix of the analyzed entity
	 *
	 * @return UsageNeedle[]
	 */
	public function computeUsageMatrix()
	{
		$url = new UsageNeedle(0.25, $this->route->getCompiled()->getRegex());
		$url->setRegex(true);

		return array(
			new UsageNeedle(1, $this->route->getActionName()),
			new UsageNeedle(0.5, $this->route->getName()),
			$url,
		);
	}
}

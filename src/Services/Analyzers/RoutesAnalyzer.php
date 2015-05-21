<?php
namespace Janitor\Services\Analyzers;

use Illuminate\Routing\Route as RouteInstance;
use Janitor\Abstracts\AbstractAnalyzer;
use Janitor\Codebase;
use Janitor\Entities\Route;
use Janitor\Exceptions\UndefinedSubjectException;
use Janitor\Interfaces\AnalyzerInterface;

class RoutesAnalyzer extends AbstractAnalyzer implements AnalyzerInterface
{
    /**
     * @type RouteInstance[]
     */
    protected $routes;

    /**
     * @param Codebase $codebase
     */
    public function __construct(Codebase $codebase)
    {
        parent::__construct($codebase);

        if ($routes = $codebase->getRoutes()) {
            $this->routes = $routes;
        }
    }

    /**
     * @param \Illuminate\Routing\Route[] $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////////////// ENTITIES //////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * Compute the entities from the information
     * that was passed to the analyzer.
     *
     * @return \Janitor\Abstracts\AbstractAnalyzedEntity[]
     */
    protected function createEntities()
    {
        $entities = [];
        if (!$this->routes) {
            throw new UndefinedSubjectException();
        }

        /** @type \Illuminate\Routing\Route $route */
        foreach ($this->routes as $route) {
            $name   = $route->getMethods()[0].' '.$route->getUri();
            $entity = new Route($this->folder, $name);
            $entity->setRoute($route);

            $entities[] = $entity;
        }

        return $entities;
    }
}

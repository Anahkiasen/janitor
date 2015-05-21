<?php
namespace Janitor\Console\Commands;

use Janitor\Abstracts\Console\AbstractAnalyzerCommand;
use Janitor\Services\Analyzers\RoutesAnalyzer;

class CleanRoutes extends AbstractAnalyzerCommand
{
    /**
     * @type string
     */
    protected $name = 'janitor:routes';

    /**
     * @type string
     */
    protected $description = 'Look for unused routes';

    /**
     * @param RoutesAnalyzer $analyzer
     */
    public function __construct(RoutesAnalyzer $analyzer)
    {
        parent::__construct();

        $this->analyzer = $analyzer;
    }
}

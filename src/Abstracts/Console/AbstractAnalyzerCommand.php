<?php
namespace Janitor\Abstracts\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Janitor\Console\DescriptorHelper;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractAnalyzerCommand extends Command
{
    /**
     * @type \Janitor\Abstracts\AbstractAnalyzer
     */
    protected $analyzer;

    /**
     * @type Collection
     */
    protected $results;

    /**
     * Execute the command.
     */
    public function fire()
    {
        $this->analyzer->setOutput($this->output);
        $this->analyzer->analyze();

        // Get unused results
        $threshold     = $this->option('threshold');
        $threshold     = is_null($threshold) ? 0 : $threshold;
        $this->results = $this->analyzer->getUnusedEntities($threshold);

        // Describe results
        $descriptor = new DescriptorHelper();
        $this->comment($this->results->count().' unused entities were found:');
        $descriptor->describe($this->output, $this->results, ['format' => $this->option('format') ?: 'txt']);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['format', null, InputOption::VALUE_REQUIRED, 'The format to get the results in'],
            ['threshold', 'T', InputOption::VALUE_REQUIRED, 'The usage threshold to use'],
        ];
    }
}

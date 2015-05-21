<?php
namespace Janitor\Entities;

use Janitor\Abstracts\AbstractAnalyzedFile;
use Janitor\UsageNeedle;

class View extends AbstractAnalyzedFile
{
    //////////////////////////////////////////////////////////////////////
    //////////////////////////// USAGE MATRIX ////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * Compute the usage matrix of the analyzed entity.
     *
     * @return UsageNeedle[]
     */
    public function computeUsageMatrix()
    {
        $extension = '.'.$this->file->getExtension();
        if (strpos($this->file->getBasename(), '.blade.php') !== false) {
            $extension = '.blade.php';
        }

        return [
            new UsageNeedle(1, $this->file->getPathname()),
            new UsageNeedle(0.5, $this->file->getBasename()),
            new UsageNeedle(0.5, $this->file->getBasename($extension)),
            new UsageNeedle(0.25, $this->getNonumeralName($this->file->getBasename($extension))),
            new UsageNeedle(0.1, $this->getUnlocalizedName($this->file->getBasename($extension))),
        ];
    }

    /**
     * Sanitize the various needles.
     *
     * @param UsageNeedle $usageNeedle
     *
     * @return UsageNeedle
     */
    public function processUsageNeedles(UsageNeedle $usageNeedle)
    {
        // Compute names from the entries
        if (count($usageNeedle->needles) === 1) {
            $usageNeedles = $this->computeNames($usageNeedle->needles[0]);
            $usageNeedle->setNeedles($usageNeedles);
        }

        return parent::processUsageNeedles($usageNeedle);
    }

    //////////////////////////////////////////////////////////////////////
    /////////////////////////////// NAMES ////////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * @param string $name
     *
     * @return string[]
     */
    protected function computeNames($name)
    {
        // Compute name with and without extension
        $name     = str_replace($this->root.'/', null, $name);
        $basename = str_replace('.'.$this->file->getExtension(), null, $name);

        return [
            $name,
            $basename,
            str_replace(DS, '.', $basename),
        ];
    }

    /**
     * Strip number from the view's name, in case of iterable views
     * called dynamically.
     *
     * @param string $basename
     *
     * @return string
     */
    protected function getNonumeralName($basename)
    {
        $basename = preg_replace('/\d+/', null, $basename);
        $basename = trim($basename, DS.'-');

        return $basename;
    }

    /**
     * Strip possible locales from the view's name, in case of
     * localized views called dynamically.
     *
     * @param string $basename
     *
     * @return string
     */
    protected function getUnlocalizedName($basename)
    {
        $basename = str_replace('.'.$this->file->getExtension(), null, $basename);
        $basename = preg_replace('/[\/-]([a-z]{2}|[a-z]{2}_[A-Z]{2})$/', null, $basename);
        $basename = trim($basename, DS.'-');

        return $basename;
    }
}

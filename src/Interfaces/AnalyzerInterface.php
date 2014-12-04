<?php
namespace Janitor\Interfaces;

use Illuminate\Support\Collection;

interface AnalyzerInterface
{
	/**
	 * Setup the files to analyze
	 *
	 * @param string $folder
	 * @param string $extensions
	 *
	 * @return void
	 */
	public function setFiles($folder, $extensions);

	/**
	 * Get the files to analyze
	 *
	 * @return Collection
	 */
	public function getFiles();

	/**
	 * Run the analyze process
	 *
	 * @return Collection
	 */
	public function analyze();
}

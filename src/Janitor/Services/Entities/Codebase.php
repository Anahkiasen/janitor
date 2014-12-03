<?php
namespace Janitor\Services\Entities;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * The user's codebase
 *
 * @author Maxime Fabre <ehtnam6@gmail.com>
 */
class Codebase
{
	/**
	 * The files that are part of the codebase
	 *
	 * @type SplFileInfo[]
	 */
	protected $files = [];

	/**
	 * Serialized version of the codebase
	 *
	 * @type string[]
	 */
	protected $serialized;

	/**
	 * Build a new codebase
	 */
	public function __construct()
	{
		$finder = new Finder();
		$files  = $finder->files()->name('/\.(php|twig)$/')->in(app_path());
		$files  = iterator_to_array($files);

		$this->files = $files;
	}

	/**
	 * Get a serialized version of the codebase
	 *
	 * @return string[]
	 */
	public function getSerialized()
	{
		if (!$this->serialized) {
			foreach ($this->files as $key => $file) {
				$this->serialized[$file->getBasename()] = $file->getContents();
			}
		}

		return $this->serialized;
	}
}

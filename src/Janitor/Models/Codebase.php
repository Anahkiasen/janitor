<?php
namespace Janitor\Models;

use Illuminate\Cache\Repository;
use Symfony\Component\Finder\Finder;

/**
 * The user's codebase
 *
 * @author Maxime Fabre <ehtnam6@gmail.com>
 */
class Codebase
{
	/**
	 * @type Repository
	 */
	private $cache;

	/**
	 * @type SplFileInfo[]
	 */
	protected $files = [];

	/**
	 * Build a new codebase
	 *
	 * @param Repository $cache
	 */
	public function __construct(Repository $cache)
	{
		$finder = new Finder();
		$files  = $finder->files()->name('/\.(php|twig)$/')->in(app_path());
		$files  = iterator_to_array($files);

		$this->files = $files;
		$this->cache = $cache;
	}

	/**
	 * Get a serialized version of the codebase
	 *
	 * @return string
	 */
	public function getSerialized()
	{
		$contents = [];
		foreach ($this->files as $key => $file) {
			$contents[$file->getBasename()] = $file->getContents();
		}

		return $contents;
	}
}

<?php
namespace Janitor\Entities;

use Illuminate\Support\Fluent;

class View extends Analyzed
{
	/**
	 * @type string[]
	 */
	protected $usageNeedles;

	/**
	 * Get the possible occurences of the view's name and
	 * their usage score
	 *
	 * @return string[]
	 */
	public function getUsageNeedles()
	{
		if (!$this->usageNeedles) {
			$extension = '.'.$this->file->getExtension();
			$needles   = array(
				['usage' => 1, 'needles' => $this->file->getPathname()],
				['usage' => 0.5, 'needles' => $this->file->getBasename()],
				['usage' => 0.25, 'needles' => $this->getNonumeralName($this->file->getBasename($extension))],
				['usage' => 0.1, 'needles' => $this->getUnlocalizedName($this->file->getBasename($extension))],
			);

			$this->usageNeedles = array_map([$this, 'sanitizeNeedles'], $needles);
		}

		return $this->usageNeedles;
	}

	/**
	 * Sanitize the various needles
	 *
	 * @param array $needle
	 *
	 * @return array
	 */
	public function sanitizeNeedles(array $needle)
	{
		$needle['needles'] = $this->computeNames($needle['needles']);
		$needle['needles'] = array_unique($needle['needles']);

		return $needle;
	}

	/**
	 * @return string
	 */
	public function getPattern()
	{
		$pattern = [];
		foreach ($this->getUsageNeedles() as $needle) {
			$pattern = array_merge($pattern, $needle['needles']);
		}

		$pattern = array_unique($pattern);
		$pattern = implode('|', $pattern);

		return $pattern;
	}

	/**
	 * Convert the Fluent instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$view            = parent::toArray();
		$view['needles'] = $this->getUsageNeedles();
		$view['pattern'] = $this->getPattern();

		return $view;
	}

	//////////////////////////////////////////////////////////////////////
	/////////////////////////////// NAMES ////////////////////////////////
	//////////////////////////////////////////////////////////////////////

	/**
	 * @param $name
	 *
	 * @return array
	 */
	protected function computeNames($name)
	{
		// Compute name with and without extension
		$name     = str_replace($this->root.'/', null, $name);
		$basename = str_replace('.'.$this->file->getExtension(), null, $name);

		return array(
			$name,
			$basename,
			str_replace(DS, '.', $basename),
		);
	}

	/**
	 * Strip number from the view's name, in case of iterable views
	 * called dynamically
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
	 * localized views called dynamically
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

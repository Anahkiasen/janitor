<?php
namespace Janitor\Models;

use Illuminate\Support\Fluent;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @property SplFileInfo file   The file itself
 * @property integer     usage  Usage percentage
 * @property string      views  Folder where the views reside
 */
class View extends Fluent
{
	/**
	 * Get the possible occurences of the view's name and
	 * their usage score
	 *
	 * @return string[]
	 */
	public function getUsageNeedles()
	{
		$needles = array(
			['usage' => 1, 'needles' => $this->file->getPathname()],
			['usage' => 0.5, 'needles' => $this->file->getBasename()],
			['usage' => 0.25, 'needles' => $this->getNonumeralName($this->file->getBasename())],
			['usage' => 0.1, 'needles' => $this->getUnlocalizedName($this->file->getBasename())],
		);

		return array_map([$this, 'sanitizeNeedles'], $needles);
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
	 * Convert the Fluent instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$view            = parent::toArray();
		$view['needles'] = $this->getUsageNeedles();

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
		$name     = str_replace($this->views.'/', null, $name);
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
		$basename = preg_replace('/[\/-]([a-z]{2}|[a-z]{2}_[A-Z]{2})$/', null, $basename);
		$basename = trim($basename, DS.'-');

		return $basename;
	}
}

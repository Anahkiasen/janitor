<?php
namespace Janitor\Entities;

class File extends Analyzed
{
	/**
	 * The file containing the entity
	 *
	 * @type SplFileInfo
	 */
	public $file;

	/**
	 * @return SplFileInfo
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param SplFileInfo $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
		$this->name = str_replace($this->root.DS, null, $this->file->getPathname());
	}
}

<?php
namespace Janitor\Entities;

use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;
use JsonSerializable;
use Symfony\Component\Finder\SplFileInfo;

class AnalyzedFile implements ArrayableInterface, JsonSerializable, JsonableInterface
{
	/**
	 * @type string
	 */
	public $root;

	/**
	 * @type string
	 */
	public $name;

	/**
	 * @type integer
	 */
	public $usage = 0;

	/**
	 * @type SplFileInfo
	 */
	public $file;

	/**
	 * @param SplFileInfo $file
	 * @param string      $root
	 */
	public function __construct(SplFileInfo $file, $root = null)
	{
		$this->file = $file;
		$this->root = $root;

		$this->name = str_replace($this->root.DIRECTORY_SEPARATOR, null, $this->file->getPathname());
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////// SERIALIZATION ///////////////////////////
	//////////////////////////////////////////////////////////////////////

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'root'  => $this->root,
			'name'  => $this->name,
			'usage' => $this->usage,
		);
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param  int $options
	 *
	 * @return string
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Specify data which should be serialized to JSON
	 *
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 *       which is a value of any type other than a resource.
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}
}

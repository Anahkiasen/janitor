<?php
namespace Janitor\Entities;

use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;
use JsonSerializable;

class Analyzed implements ArrayableInterface, JsonSerializable, JsonableInterface
{
	/**
	 * The root path where analyzed entities reside
	 *
	 * @type string
	 */
	public $root;

	/**
	 * The base name of the analyzed entity
	 *
	 * @type string
	 */
	public $name;

	/**
	 * The usage certainty:
	 * 0: certainly unused
	 * 1: certainly used
	 *
	 * @type integer
	 */
	public $usage = 0;

	/**
	 * @param string $root
	 * @param string $name
	 */
	public function __construct($root, $name)
	{
		$this->name = $name;
		$this->root = $root;
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

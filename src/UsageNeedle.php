<?php
namespace Janitor;

class UsageNeedle
{
	/**
	 * @type integer
	 */
	public $usage;

	/**
	 * @type string[]
	 */
	public $needles;

	/**
	 * @param integer $usage
	 * @param string  $needles
	 */
	public function __construct($usage, $needles)
	{
		$this->usage   = $usage;
		$this->needles = $needles;
	}
}

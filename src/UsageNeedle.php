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
	 * Whether the needles are regexes or not
	 *
	 * @type boolean
	 */
	protected $regex;

	/**
	 * @param integer $usage
	 * @param string  $needles
	 * @param boolean $regex
	 */
	public function __construct($usage, $needles, $regex = false)
	{
		$this->usage   = $usage;
		$this->needles = $needles;
		$this->regex   = $regex;
	}

	/**
	 * @return boolean
	 */
	public function isRegex()
	{
		return $this->regex;
	}

	/**
	 * @param boolean $regex
	 */
	public function setRegex($regex)
	{
		$this->regex = $regex;
	}

	/**
	 * Check if the usage needle
	 *
	 * @param $token
	 *
	 * @return boolean
	 */
	public function matches($token)
	{
		foreach ($this->needles as $needle) {
			if ($this->regex) {
				return preg_match($needle, $token);
			}

			return strpos($token, $needle) !== false;
		}
	}
}

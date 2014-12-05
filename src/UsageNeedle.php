<?php
namespace Janitor;

class UsageNeedle
{
	/**
	 * @type integer
	 */
	public $usage;

	/**
	 * @type string|string[]
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
		$this->regex   = $regex;

		$this->setNeedles($needles);
	}

	/**
	 * @param string|\string[] $needles
	 */
	public function setNeedles($needles)
	{
		$this->needles = $this->unifyNeedles($needles);
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
				return (bool) preg_match($needle, $token);
			}

			return $this->contains($token, $needle) || $this->looselyMatches($token, $needle);
		}

		return false;
	}

	//////////////////////////////////////////////////////////////////////
	////////////////////////////// HELPERS ///////////////////////////////
	//////////////////////////////////////////////////////////////////////

	/**
	 * Check if a needle is contained in a token
	 *
	 * @param string $token
	 * @param string $needle
	 *
	 * @return boolean
	 */
	public function contains($token, $needle)
	{
		return strpos($token, $needle) !== false;
	}

	/**
	 * Check if a needle loosely matches a token
	 *
	 * @param string $token
	 * @param string $needle
	 *
	 * @return boolean
	 */
	public function looselyMatches($token, $needle)
	{
		if (strlen($token) >= 255 || strlen($needle) >= 255) {
			return false;
		}

		// Unify casing
		$token  = strtolower($token);
		$needle = strtolower($needle);

		// Unify quotes
		$token = str_replace('"', "'", $token);
		$needle = str_replace('"', "'", $needle);

		// Compute Levenshtein distance
		$distance = levenshtein($needle, $token);
		if ($distance > 3) {
			return false;
		}

		// Affect usage
		$this->usage -= ($distance * 0.1);

		return true;
	}

	/**
	 * Unify and sanitize the needles
	 *
	 * @param string|string[] $needles
	 *
	 * @return string[]
	 */
	protected function unifyNeedles($needles)
	{
		$needles = (array) $needles;
		$needles = array_filter($needles);
		$needles = array_unique($needles);

		return $needles;
	}
}

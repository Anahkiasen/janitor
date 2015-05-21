<?php
namespace Janitor;

class UsageNeedle
{
    /**
     * @type int
     */
    public $usage;

    /**
     * @type string|string[]
     */
    public $needles;

    /**
     * Whether the needles are regexes or not.
     *
     * @type bool
     */
    protected $regex;

    /**
     * Enable loose matching.
     *
     * @type bool
     */
    protected $loose = false;

    /**
     * @param int    $usage
     * @param string $needles
     * @param bool   $regex
     */
    public function __construct($usage, $needles, $regex = false)
    {
        $this->usage = $usage;
        $this->regex = $regex;

        $this->setNeedles($needles);
    }

    //////////////////////////////////////////////////////////////////////
    /////////////////////////////// OPTIONS //////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * @param string|\string[] $needles
     */
    public function setNeedles($needles)
    {
        $this->needles = $this->unifyNeedles($needles);
    }

    /**
     * @return bool
     */
    public function isRegex()
    {
        return $this->regex;
    }

    /**
     * @param bool $regex
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;
    }

    /**
     * @return bool
     */
    public function isLoose()
    {
        return $this->loose;
    }

    /**
     * @param bool $loose
     */
    public function setLoose($loose)
    {
        $this->loose = $loose;
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////////////// MATCHING///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * Check if the usage needle.
     *
     * @param $token
     *
     * @return bool
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

    /**
     * Check if a needle is contained in a token.
     *
     * @param string $token
     * @param string $needle
     *
     * @return bool
     */
    public function contains($token, $needle)
    {
        return strpos($token, $needle) !== false;
    }

    /**
     * Check if a needle loosely matches a token.
     *
     * @param string $token
     * @param string $needle
     *
     * @return bool
     */
    public function looselyMatches($token, $needle)
    {
        if (!$this->loose || strlen($token) >= 255 || strlen($needle) >= 255) {
            return false;
        }

        // Unify casing
        $token  = strtolower($token);
        $needle = strtolower($needle);

        // Unify quotes
        $token  = str_replace('"', "'", $token);
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
     * Unify and sanitize the needles.
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

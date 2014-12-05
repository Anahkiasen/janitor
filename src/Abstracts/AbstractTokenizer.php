<?php
namespace Janitor\Abstracts;

class AbstractTokenizer
{
	/**
	 * Tokens to ignore
	 *
	 * @type array
	 */
	protected $ignored = [];

	/**
	 * @param array $tokens
	 *
	 * @return array
	 */
	protected function filterTokens(array $tokens)
	{
		return array_filter($tokens, function ($token) {
			return !in_array($token, $this->ignored);
		});
	}
}

<?php
namespace Janitor\Services\Tokenizers;

use Janitor\Interfaces\TokenizerInterface;

class PhpTokenizer implements TokenizerInterface
{
	/**
	 * Tokenize a file
	 *
	 * @param string $file
	 *
	 * @return string[]
	 */
	public function tokenize($file)
	{
		// Extract all string tokens
		$tokens = token_get_all($file);
		$tokens = array_filter($tokens, function ($token) {
			return is_array($token) && $token[0] === T_CONSTANT_ENCAPSED_STRING;
		});

		// Unwrap array and strings
		$tokens = array_map(function ($token) {
			return trim($token[1], "'");
		}, $tokens);

		return $tokens;
	}
}

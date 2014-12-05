<?php
namespace Janitor\Services\Tokenizers;

use Symfony\Component\Yaml\Yaml;

class YamlTokenizer extends PhpTokenizer
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
		$file = Yaml::parse($file);
		$file = $this->toPhpFile($file);

		return parent::tokenize($file);
	}
}

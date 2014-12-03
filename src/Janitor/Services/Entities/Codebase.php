<?php
namespace Janitor\Services\Entities;

use Janitor\Services\Tokenizers\DefaultTokenizer;
use Janitor\Services\Tokenizers\PhpTokenizer;
use Janitor\Services\Tokenizers\TwigTokenizer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Janitor\Interfaces\TokenizerInterface;

/**
 * The user's codebase
 *
 * @author Maxime Fabre <ehtnam6@gmail.com>
 */
class Codebase
{
	/**
	 * The files that are part of the codebase
	 *
	 * @type SplFileInfo[]
	 */
	protected $files = [];

	/**
	 * Serialized version of the codebase
	 *
	 * @type string[]
	 */
	protected $tokenized;

	/**
	 * Build a new codebase
	 */
	public function __construct()
	{
		$finder = new Finder();
		$files  = $finder->files()->name('/\.(php|twig)$/')->in(app_path());
		$files  = iterator_to_array($files);

		$this->files = $files;
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////// TOKENIZATION ////////////////////////////
	//////////////////////////////////////////////////////////////////////

	/**
	 * Get a serialized version of the codebase
	 *
	 * @return string[]
	 */
	public function getTokenized()
	{
		if (!$this->tokenized) {
			foreach ($this->files as $key => $file) {
				$this->tokenized[$file->getBasename()] = $this->extractStringTokens($file);
			}
		}

		return $this->tokenized;
	}

	/**
	 * Extract all strings from a given file
	 *
	 * @param SplFileInfo $file
	 *
	 * @return string[]
	 */
	protected function extractStringTokens(SplFileInfo $file)
	{
		// Get the contents of the file
		$contents = $file->getContents();

		// See if we have an available Tokenizer
		// and use it to extract the contents
		switch ($file->getExtension()) {
			case 'php':
				$tokenizer = new PhpTokenizer();
				break;

			case 'twig':
				$tokenizer = new TwigTokenizer();
				break;

			default:
				$tokenizer = new DefaultTokenizer();
				break;
		}

		return $tokenizer->tokenize($contents);
	}
}

<?php
namespace Janitor\Services\Tokenizers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;

class BladeTokenizer extends PhpTokenizer
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
		// Transform into PHP file
		$compiler = new BladeCompiler(new Filesystem(), 'cache');
		$compiled = $compiler->compileString($file);

		return parent::tokenize($compiled);
	}
}

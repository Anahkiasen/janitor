<?php
namespace Janitor\Services\Tokenizers;

use Janitor\Interfaces\TokenizerInterface;

class DefaultTokenizer implements TokenizerInterface
{
    /**
     * Tokenize a file.
     *
     * @param string $file
     *
     * @return string[]
     */
    public function tokenize($file)
    {
        return [$file];
    }
}

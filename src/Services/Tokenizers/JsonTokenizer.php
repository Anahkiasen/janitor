<?php
namespace Janitor\Services\Tokenizers;

class JsonTokenizer extends PhpTokenizer
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
        // Transform the JSON file into a PHP array file
        $file = json_decode($file, true);
        $file = $this->toPhpFile($file);

        return parent::tokenize($file);
    }
}
